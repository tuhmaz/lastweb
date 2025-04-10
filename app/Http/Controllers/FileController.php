<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Article;
use App\Models\Subject;
use App\Models\Semester;
use App\Models\SchoolClass;
use App\Models\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class FileController extends Controller
{
  private function getConnection(string $country): string
  {
    return match ($country) {
      '2' => 'sa',  // السعودية
      '3' => 'eg',  // مصر
      '4' => 'ps',  // فلسطين
      default => 'jo', // الأردن
    };
  }

  public function index(Request $request)
  {
    $country = $request->input('country', '1');
    $connection = $this->getConnection($country);

    // بناء الاستعلام
    $query = File::on($connection)->with('article');

    // تصفية حسب الفئة
    if ($request->has('category')) {
      $query->where('file_category', $request->category);
    }

    // البحث حسب عنوان المقالة
    if ($request->has('search')) {
      $search = $request->search;
      $query->whereHas('article', function($q) use ($search) {
        $q->where('title', 'like', "%{$search}%");
      });
    }

    // جلب المقالات للقائمة المنسدلة في نموذج الرفع
    $articles = Article::on($connection)->get();

    // جلب الملفات مع التصنيف
    $files = $query->latest()->paginate(10);

    return view('content.dashboard.files.index', compact('files', 'articles', 'country'));
  }

  public function create(Request $request)
  {
    $country = $request->input('country', '1');
    $connection = $this->getConnection($country);


    $articles = Article::on($connection)->get();

    return view('content.dashboard.files.create', compact('articles', 'country'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'article_id' => 'required|exists:articles,id',
      'file' => 'required|file',
      'file_category' => 'required|string',
    ]);

    $country = $request->input('country', '1');
    $connection = $this->getConnection($country);

    $article = Article::on($connection)->findOrFail($request->article_id);
    $class_name = $article->schoolClass->grade_name;

    if ($request->hasFile('file')) {
      $file = $request->file('file');
      $originalName = $file->getClientOriginalName();
      $safeFileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

      // تحديد المسار الصحيح للحفظ
      $storagePath = public_path('storage/files/' . Str::slug($country) . '/' . Str::slug($class_name) . '/' . $request->file_category);

      // إنشاء المجلد إذا لم يكن موجوداً
      if (!file_exists($storagePath)) {
          mkdir($storagePath, 0777, true);
      }

      // حفظ الملف في المسار المحدد
      $file->move($storagePath, $safeFileName);

      // تخزين المسار النسبي في قاعدة البيانات
      $relativePath = 'storage/files/' . Str::slug($country) . '/' . Str::slug($class_name) . '/' . $request->file_category . '/' . $safeFileName;

      $fileModel = File::on($connection)->create([
        'article_id' => $request->article_id,
        'file_path' => $relativePath,
        'file_type' => $file->getClientOriginalExtension(),
        'file_category' => $request->file_category,
        'file_Name' => $originalName,
      ]);

      return response()->json([
        'success' => true,
        'message' => __('File uploaded successfully'),
        'file' => [
          'id' => $fileModel->id,
          'name' => $fileModel->file_Name,
          'path' => $fileModel->file_path,
          'type' => $fileModel->file_type,
          'category' => $fileModel->file_category
        ]
      ]);
    }

    return response()->json([
      'success' => false,
      'message' => __('No file was uploaded')
    ], 400);
  }

  public function show(Request $request, $id)
  {
    $country = $request->input('country', '1');
    $connection = $this->getConnection($country);

    // جلب الملف مع المقالة المرتبطة وكل العلاقات المطلوبة
    $file = File::on($connection)
      ->with(['article' => function($query) {
        $query->with(['schoolClass', 'subject', 'semester']);
      }])
      ->findOrFail($id);

    return view('content.dashboard.files.show', compact('file', 'country'));
  }

  public function download(Request $request, $id)
  {
    try {
      $country = $request->input('country', '1');
      $connection = $this->getConnection($country);

      // جلب الملف
      $file = File::on($connection)->findOrFail($id);

      // التحقق من مسار الملف
      $filePath = storage_path('app/public/' . $file->file_path);

      if (!file_exists($filePath)) {
        return response()->json([
          'error' => __('File not found')
        ], 404);
      }

      // تحديث عدد مرات التحميل
      if (Schema::connection($connection)->hasColumn('files', 'download_count')) {
        $file->increment('download_count');
      }

      // تحميل الملف
      return response()->download($filePath, $file->file_Name);

    } catch (\Exception $e) {
      return response()->json([
        'error' => __('Error downloading file')
      ], 500);
    }
  }

  public function edit(Request $request, $id)
  {
    $country = $request->input('country', '1');
    $connection = $this->getConnection($country);

    $file = File::on($connection)->findOrFail($id);
    $articles = Article::on($connection)->get();

    return view('content.dashboard.files.edit', compact('file', 'articles', 'country'));
  }

  public function update(Request $request, $id)
  {
    try {
      $country = $request->input('country', '1');
      $connection = $this->getConnection($country);

      // التحقق من صحة البيانات
      $request->validate([
        'article_id' => 'required|exists:' . $connection . '.articles,id',
        'file_category' => 'required|in:plans,papers,tests,books',
        'file' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx'
      ]);

      // جلب الملف الحالي
      $file = File::on($connection)->findOrFail($id);
      $oldFilePath = $file->file_path;

      // تحديث البيانات الأساسية
      $file->article_id = $request->article_id;
      $file->file_category = $request->file_category;

      // معالجة الملف الجديد إذا تم تحميله
      if ($request->hasFile('file')) {
        $uploadedFile = $request->file('file');
        $fileName = time() . '_' . Str::slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME))
                   . '.' . $uploadedFile->getClientOriginalExtension();

        // حذف الملف القديم
        if ($oldFilePath && Storage::exists('public/' . $oldFilePath)) {
          Storage::delete('public/' . $oldFilePath);
        }

        // تخزين الملف الجديد
        $path = $uploadedFile->storeAs(
          'public/files/' . $file->file_category,
          $fileName
        );

        // تحديث معلومات الملف
        $file->file_Name = $uploadedFile->getClientOriginalName();
        $file->file_path = str_replace('public/', '', $path);
        $file->file_type = $uploadedFile->getClientOriginalExtension();
      }

      $file->save();

      return response()->json([
        'success' => true,
        'message' => __('File updated successfully')
      ]);

    } catch (\Exception $e) {
      \Log::error('File update error: ' . $e->getMessage());

      return response()->json([
        'success' => false,
        'message' => __('Error updating file: ') . $e->getMessage()
      ], 500);
    }
  }

  public function destroy(Request $request, $id)
  {
    $country = $request->input('country', '1');
    $connection = $this->getConnection($country);

    $file = File::on($connection)->findOrFail($id);

    try {
      if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
        Storage::disk('public')->delete($file->file_path);
      }

      $file->delete();

      return redirect()->route('files.index', ['country' => $country])->with('success', 'File deleted successfully.');
    } catch (\Exception $e) {
      Log::error('Error deleting file: ' . $e->getMessage());
      return redirect()->route('files.index', ['country' => $country])->with('error', 'Error deleting file.');
    }
  }



  public function showFilterPage()
  {
    $classes = SchoolClass::all();
    $semesters = Semester::all();
    $subjects = Subject::all();

    return view('frontend.filter-files', compact('classes', 'semesters', 'subjects'));
  }

  public function filter(Request $request)
  {
    $query = File::query();

    if ($request->has('class_id') && $request->class_id) {
      $query->whereHas('article.subject.schoolClass', function ($q) use ($request) {
        $q->where('id', $request->class_id);
      });
    }

    if ($request->has('semester_id') && $request->semester_id) {
      $query->whereHas('article', function ($q) use ($request) {
        $q->where('semester_id', $request->semester_id);
      });
    }

    if ($request->has('subject_id') && $request->subject_id) {
      $query->whereHas('article', function ($q) use ($request) {
        $q->where('subject_id', $request->subject_id);
      });
    }

    if ($request->has('file_category') && $request->file_category) {
      $query->where('file_type', $request->file_category);
    }

    $files = $query->get();
    if ($files->isEmpty()) {
      return redirect()->back()->with('error', 'No files found matching the selected criteria.');
    }

    $classes = SchoolClass::all();
    $semesters = Semester::all();
    $subjects = Subject::all();

    return view('frontend.filter-files', compact('files', 'classes', 'semesters', 'subjects'));
  }


  public function downloadFile(Request $request, $id)
  {
    try {
      $database = $request->query('database', session('database', 'jo'));

      if (!$database || !config('database.connections.' . $database)) {
        return redirect()->back()->with('error', 'Invalid database configuration.');
      }

      $file = File::on($database)->findOrFail($id);

      if (!$file) {
        return redirect()->back()->with('error', 'File not found.');
      }

      $filePath = storage_path('app/public/' . $file->file_path);

      if (!file_exists($filePath)) {
        return redirect()->back()->with('error', 'File does not exist on the server.');
      }

      $file->increment('download_count');

      return response()->download($filePath, $file->file_Name);

    } catch (\Exception $e) {
      \Log::error('File download error: ' . $e->getMessage());
      return redirect()->back()->with('error', 'An error occurred while downloading the file.');
    }
  }

  public function showDownloadPage($fileId)
  {
    try {
      $database = session('database', 'jo');

      if (!config('database.connections.' . $database)) {
        abort(500, 'Invalid database configuration.');
      }

      $file = File::on($database)->findOrFail($fileId);

      if (!$file) {
        abort(404, 'File not found.');
      }

      $filePath = storage_path('app/public/' . $file->file_path);

      if (!file_exists($filePath)) {
        abort(404, 'File does not exist on the server.');
      }

      $pageTitle = 'تحميل الملف: ' . $file->file_Name;

      return view('content.frontend.download.download-page', compact('file', 'pageTitle'));
    } catch (\Exception $e) {
      \Log::error('Show download page error: ' . $e->getMessage());
      abort(500, 'An error occurred while processing your request.');
    }
  }

  public function processDownload($fileId)
  {
    try {
      $database = session('database', 'jo');

      if (!config('database.connections.' . $database)) {
        abort(500, 'Invalid database configuration.');
      }

      $file = File::on($database)->findOrFail($fileId);

      if (!$file) {
        abort(404, 'File not found.');
      }

      $filePath = storage_path('app/public/' . $file->file_path);

      if (!file_exists($filePath)) {
        abort(404, 'File does not exist on the server.');
      }

      $file->increment('download_count');

      return response()->download($filePath, $file->file_Name);
    } catch (\Exception $e) {
      \Log::error('Process download error: ' . $e->getMessage());
      abort(500, 'An error occurred while processing your download.');
    }
  }
}
