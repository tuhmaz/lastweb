<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Article;
use App\Models\Subject;
use App\Models\Semester;
use App\Models\SchoolClass;
use App\Models\File;
use App\Models\User;
use App\Models\Keyword;
use App\Notifications\ArticleNotification;
use App\Services\SecureFileUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use OneSignal;

class ArticleController extends Controller
{
  /**
   * خدمة التحميل الآمن للملفات
   */
  protected $secureFileUploadService;

  /**
   * إنشاء مثيل جديد من وحدة التحكم.
   */
  public function __construct(SecureFileUploadService $secureFileUploadService)
  {
      $this->secureFileUploadService = $secureFileUploadService;
      $this->middleware('auth');
  }

  private function getAvailableCountries(): array
  {
      return [
          ['id' => 1, 'name' => 'الأردن', 'code' => 'jo'],
          ['id' => 2, 'name' => 'السعودية', 'code' => 'sa'],
          ['id' => 3, 'name' => 'مصر', 'code' => 'eg'],
          ['id' => 4, 'name' => 'فلسطين', 'code' => 'ps'],
      ];
  }

  /**
   * تحديد اتصال قاعدة البيانات بناءً على معرف الدولة
   */
  private function getConnection($countryId = null): string
  {
      if (!$countryId) {
          return 'jo'; // الأردن كقيمة افتراضية
      }

      // تحويل المعرف إلى رقم
      $countryId = (int) $countryId;

      return match ($countryId) {
          2 => 'sa',  // السعودية
          3 => 'eg',  // مصر
          4 => 'ps',  // فلسطين
          default => 'jo', // الأردن
      };
  }


  public function index(Request $request)
  {
      $country = $request->input('country', 1); // القيمة الافتراضية 1 للأردن
      $connection = $this->getConnection($country);

      $articles = Article::on($connection)
          ->with(['schoolClass', 'subject', 'semester', 'keywords'])

          ->paginate(25);

      return view('content.dashboard.articles.index', compact('articles', 'country'));
  }

  public function create(Request $request)
  {
      $country = $request->input('country', '1');
      $connection = $this->getConnection($country);

      $classes = SchoolClass::on($connection)->get();
      $subjects = Subject::on($connection)->get();
      $semesters = Semester::on($connection)->get();

      return view('content.dashboard.articles.create', compact('classes', 'subjects', 'semesters', 'country'));
  }

  public function store(Request $request)
  {
      $validated = $request->validate([
          'country' => 'required|string',
          'class_id' => 'required|exists:school_classes,id',
          'subject_id' => 'required|exists:subjects,id',
          'semester_id' => 'required|exists:semesters,id',
          'title' => 'required|string|max:60',
          'content' => 'required',
          'keywords' => 'nullable|string',
          'file_category' => 'required|string',
          'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar,jpg,jpeg,png,gif,webp|max:10240',
          'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120|dimensions:min_width=100,min_height=100',
          'meta_description' => 'nullable|string|max:120',
      ]);

      $country = $request->input('country', '1');
      $connection = $this->getConnection($country);

      DB::connection($connection)->transaction(function () use ($request, $validated, $country, $connection) {

          $metaDescription = $request->meta_description;

          if ($request->use_title_for_meta && !$metaDescription) {
              $metaDescription = Str::limit($request->title, 120);
          }

          if ($request->use_keywords_for_meta && !$metaDescription && $request->keywords) {
              $metaDescription = Str::limit($request->keywords, 120);
          }

          if (!$metaDescription) {
              $metaDescription = Str::limit(strip_tags($request->content), 120);
          }

          // معالجة صورة المقال إذا وجدت
          $imagePath = 'default.webp';
          if ($request->hasFile('image')) {
              $imagePath = $this->securelyStoreArticleImage($request->file('image'));
          }

          $article = Article::on($connection)->create([
              'grade_level' => $request->class_id,
              'subject_id' => $request->subject_id,
              'semester_id' => $request->semester_id,
              'title' => $request->title,
              'content' => $request->content,
              'meta_description' => $metaDescription,
              'author_id' => Auth::id(),
              'status' => $request->has('status') ? true : false,
              'image' => $imagePath,
          ]);

          if ($request->keywords) {
              $keywords = array_map('trim', explode(',', $request->keywords));

              foreach ($keywords as $keyword) {
                  $keywordModel = Keyword::on($connection)->firstOrCreate(['keyword' => $keyword]);
                  $article->keywords()->attach($keywordModel->id);
              }
          }

          if ($request->hasFile('file')) {
              try {
                  $schoolClass = SchoolClass::on($connection)->find($request->class_id);
                  $folderName = $schoolClass ? $schoolClass->grade_name : 'General';
                  $folderCategory = Str::slug($request->file_category);

                  $country = $request->input('country', 'default_country');
                  $countrySlug = Str::slug($country);
                  $folderNameSlug = Str::slug($folderName);
                  
                  // تحديد المسار الكامل للمجلد
                  $folderPath = "files/$countrySlug/$folderNameSlug/$folderCategory";
                  
                  // استخدام اسم الملف المخصص إذا تم توفيره
                  $customFilename = $request->input('file_name');
                  
                  // تخزين الملف بشكل آمن
                  $fileInfo = $this->securelyStoreAttachment(
                      $request->file('file'),
                      $folderPath,
                      $customFilename
                  );

                  // إنشاء سجل الملف في قاعدة البيانات
                  File::on($connection)->create([
                      'article_id' => $article->id,
                      'file_path' => $fileInfo['path'],
                      'file_type' => $fileInfo['extension'],
                      'file_category' => $request->file_category,
                      'file_name' => $fileInfo['filename'],
                      'file_size' => $fileInfo['size'],
                      'mime_type' => $fileInfo['mime_type']
                  ]);
              } catch (\Exception $e) {
                  // تسجيل الخطأ
                  \Illuminate\Support\Facades\Log::error('فشل في تخزين الملف المرفق: ' . $e->getMessage());
              }
          }
          
          $this->sendNotification($article);

          $users = User::all();
          foreach ($users as $user) {
              $user->notify(new ArticleNotification($article));
          }
      });


      return redirect()->route('dashboard.articles.index', ['country' => $country])->with('success', 'Article created successfully.');
  }

  protected function sendNotification($article)
  {
      try {
          if (config('onesignal.app_id') && config('onesignal.rest_api_key')) {
              OneSignal::sendNotificationToAll(
                  "تم نشر مقال جديد: {$article->title} (الصف: " . SchoolClass::on($article->connection)->find($article->grade_level)->grade_name . ")",
                  $url = null,
                  $data = null,
                  $buttons = null,
                  $schedule = null
              );
          }
      } catch (\Exception $e) {
          \Log::warning('Failed to send notification: ' . $e->getMessage());
          // Continue execution even if notification fails
      }
  }

  public function show(Request $request, $id)
  {
      $country = $request->input('country', '1');
      $connection = $this->getConnection($country);
      $article = Article::on($connection)->with(['files', 'subject', 'semester', 'schoolClass', 'keywords'])->findOrFail($id);
      $article->increment('visit_count');
      $contentWithKeywords = $this->replaceKeywordsWithLinks($article->content, $article->keywords);
      $article->content = $this->createInternalLinks($article->content, $article->keywords);

      return view('content.dashboard.articles.show', compact('article', 'contentWithKeywords', 'country'));
  }

  private function replaceKeywordsWithLinks($content, $keywords)
  {
      foreach ($keywords as $keyword) {
        $database = session('database', 'jo');
          $keywordText = $keyword->keyword;
          $keywordLink = route('keywords.indexByKeyword', ['database' => $database,'keywords' => $keywordText]);
          $content = preg_replace('/\b' . preg_quote($keywordText, '/') . '\b/', '<a href="' . $keywordLink . '">' . $keywordText . '</a>', $content);
      }

      return $content;
  }

  public function edit(Request $request, $id)
  {
      $country = $request->input('country', '1');
      $connection = $this->getConnection($country);

      $article = Article::on($connection)->with('files')->findOrFail($id);
      $classes = SchoolClass::on($connection)->get();
      $subjects = Subject::on($connection)->where('grade_level', $article->grade_level)->get();
      $semesters = Semester::on($connection)->where('grade_level', $article->grade_level)->get();

      return view('content.dashboard.articles.edit', compact('article', 'classes', 'subjects', 'semesters', 'country'));
  }

  public function update(Request $request, $id)
  {
      $validated = $request->validate([
          'class_id' => 'required|exists:school_classes,id',
          'subject_id' => 'required|exists:subjects,id',
          'semester_id' => 'required|exists:semesters,id',
          'title' => 'required|string|max:60',
          'content' => 'required',
          'keywords' => 'nullable|string',
          'file_category' => 'required|string',
          'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar,jpg,jpeg,png,gif,webp|max:10240',
          'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120|dimensions:min_width=100,min_height=100',
          'meta_description' => 'nullable|string|max:120',
      ]);

      $country = $request->input('country', '1');
      $connection = $this->getConnection($country);

      $article = Article::on($connection)->findOrFail($id);

      $metaDescription = $request->meta_description;
      if (!$metaDescription) {
          if ($request->keywords) {
              $metaDescription = Str::limit($request->keywords, 120);
          } else {
              $metaDescription = Str::limit(strip_tags($request->content), 120);
          }
      }

      $article->subject_id = $request->subject_id;
      $article->semester_id = $request->semester_id;
      $article->title = $request->title;
      $article->content = $request->content;
      $article->meta_description = $metaDescription;
      $article->status = $request->has('status') ? true : false;

      // معالجة صورة المقال إذا تم تحميلها
      if ($request->hasFile('image')) {
          try {
              // حذف الصورة القديمة إذا كانت موجودة
              if ($article->image && $article->image !== 'default.webp') {
                  Storage::disk('public')->delete('images/articles/' . $article->image);
              }
              
              // تخزين الصورة الجديدة بشكل آمن
              $article->image = $this->securelyStoreArticleImage($request->file('image'));
          } catch (\Exception $e) {
              // تسجيل الخطأ
              \Illuminate\Support\Facades\Log::error('فشل في تحديث صورة المقال: ' . $e->getMessage());
          }
      }

      $article->save();

      if ($request->keywords) {
          $keywords = array_map('trim', explode(',', $request->keywords));

          $article->keywords()->detach();

          foreach ($keywords as $keyword) {
              $keywordModel = Keyword::on($connection)->firstOrCreate(['keyword' => $keyword]);
              $article->keywords()->attach($keywordModel->id);
          }
      }

      if ($request->hasFile('new_file')) {
          try {
              // حذف الملف الحالي إذا وجد
              $currentFile = $article->files->first();
              if ($currentFile) {
                  Storage::disk('public')->delete($currentFile->file_path);
                  $currentFile->delete();
              }

              // إعداد مسار المجلد
              $schoolClass = SchoolClass::on($connection)->find($request->class_id);
              $folderName = $schoolClass ? $schoolClass->grade_name : 'General';
              $folderNameSlug = Str::slug($folderName);
              $folderCategory = Str::slug($request->file_category);
              $folderPath = "files/$folderNameSlug/$folderCategory";
              
              // استخدام اسم الملف المخصص إذا تم توفيره
              $customFilename = $request->input('file_name');
              
              // تخزين الملف الجديد بشكل آمن
              $fileInfo = $this->securelyStoreAttachment(
                  $request->file('new_file'),
                  $folderPath,
                  $customFilename
              );

              // إنشاء سجل الملف في قاعدة البيانات
              File::on($connection)->create([
                  'article_id' => $article->id,
                  'file_path' => $fileInfo['path'],
                  'file_type' => $fileInfo['extension'],
                  'file_category' => $request->file_category,
                  'file_name' => $fileInfo['filename'],
                  'file_size' => $fileInfo['size'],
                  'mime_type' => $fileInfo['mime_type']
              ]);
          } catch (\Exception $e) {
              // تسجيل الخطأ
              \Illuminate\Support\Facades\Log::error('فشل في تحديث الملف المرفق: ' . $e->getMessage());
          }
      }

      return redirect()->route('dashboard.articles.index', ['country' => $country])->with('success', 'Article updated successfully.');
  }

  public function destroy(Request $request, $id)
{
    $country = $request->input('country', '1');
    $connection = $this->getConnection($country);

    // جلب المقال مع الصور والملفات المرتبطة به
    $article = Article::on($connection)->with('files')->findOrFail($id);

    // حذف الصورة المرتبطة بالمقال (إذا كانت موجودة وليست الصورة الافتراضية)
    $imageName = $article->image;

    if ($imageName && $imageName !== 'default.webp') {
        if (Storage::disk('public')->exists('images/' . $imageName)) {
            Storage::disk('public')->delete('images/' . $imageName);
        }
    }

    // حذف الملفات المرتبطة بالمقال
    foreach ($article->files as $file) {
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();
    }

    // حذف المقال من قاعدة البيانات
    $article->delete();

    return redirect()->route('dashboard.articles.index', ['country' => $country])
        ->with('success', 'تم حذف المقال والصور والملفات المرتبطة به بنجاح.');
}

  public function indexByClass(Request $request, $grade_level)
  {
      $country = $request->input('country', '1');
      $connection = $this->getConnection($country);

      $articles = Article::on($connection)
          ->whereHas('subject', function ($query) use ($grade_level) {
              $query->where('grade_level', $grade_level);
          })
          ->get();

      return view('content.dashboard.articles.index', compact('articles', 'country'));
  }

  private function createInternalLinks($content, $keywords)
  {
      $keywordsArray = $keywords->pluck('keyword')->toArray();

      foreach ($keywordsArray as $keyword) {
        $database = session('database', 'jo');
          $keyword = trim($keyword);
          $url = route('keywords.indexByKeyword', ['database' => $database,'keywords' => $keyword]);
          $content = str_replace($keyword, '<a href="' . $url . '">' . $keyword . '</a>', $content);
      }

      return $content;
  }

  public function indexByKeyword(Request $request, $keyword)
  {
      $country = $request->input('country', '1');
      $connection = $this->getConnection($country);
      $keywordModel = Keyword::on($connection)->where('keyword', $keyword)->firstOrFail();
      $articles = $keywordModel->articles()->get();
      return view('frontend.keywords.keyword', [
          'articles' => $articles,
          'keyword' => $keywordModel
      ]);
  }

  /**
   * نشر المقال
   */
  public function publish(Request $request, $id)
  {
      try {
          $country = $request->input('country', 1); // القيمة الافتراضية 1 للأردن
          $connection = $this->getConnection($country);

          $article = Article::on($connection)->findOrFail($id);
          $article->status = true;
          $article->save();

          return response()->json([
              'success' => true,
              'message' => __('Article published successfully')
          ]);

      } catch (\Exception $e) {
          \Log::error('Error publishing article: ' . $e->getMessage());
          return response()->json([
              'success' => false,
              'message' => __('Failed to publish article')
          ], 500);
      }
  }

  /**
   * إلغاء نشر المقال
   */
  public function unpublish(Request $request, $id)
  {
      try {
          $country = $request->input('country', 1); // القيمة الافتراضية 1 للأردن
          $connection = $this->getConnection($country);

          $article = Article::on($connection)->findOrFail($id);
          $article->status = false;
          $article->save();

          return response()->json([
              'success' => true,
              'message' => __('Article unpublished successfully')
          ]);

      } catch (\Exception $e) {
          \Log::error('Error unpublishing article: ' . $e->getMessage());
          return response()->json([
              'success' => false,
              'message' => __('Failed to unpublish article')
          ], 500);
      }
  }

  /**
   * تخزين الملف المرفق بشكل آمن
   * 
   * @param \Illuminate\Http\UploadedFile $file الملف المرفوع
   * @param string $folderPath المسار الذي سيتم تخزين الملف فيه
   * @param string $filename اسم الملف (اختياري)
   * @return array مصفوفة تحتوي على معلومات الملف المخزن
   */
  private function securelyStoreAttachment($file, $folderPath, $filename = null)
  {
      try {
          // إذا لم يتم تحديد اسم للملف، استخدم الاسم الأصلي
          $originalFilename = $file->getClientOriginalName();
          $finalFilename = $filename ?: $originalFilename;
          
          // تخزين الملف بشكل آمن مع الحفاظ على الامتداد الأصلي
          $path = $this->secureFileUploadService->securelyStoreFile(
              $file, 
              $folderPath, 
              false, // لا نريد معالجة الملفات العادية كصور
              $finalFilename
          );
          
          // إرجاع معلومات الملف
          return [
              'path' => $path,
              'filename' => $finalFilename,
              'size' => $file->getSize(),
              'mime_type' => $file->getMimeType(),
              'extension' => $file->getClientOriginalExtension()
          ];
      } catch (\Exception $e) {
          // تسجيل الخطأ
          \Illuminate\Support\Facades\Log::error('فشل في تخزين الملف المرفق: ' . $e->getMessage());
          throw $e;
      }
  }

  /**
   * تخزين صورة المقال بشكل آمن
   * 
   * @param \Illuminate\Http\UploadedFile $file الملف المرفوع
   * @return string مسار الصورة المخزنة
   */
  private function securelyStoreArticleImage($file)
  {
      try {
          // تخزين الصورة بشكل آمن مع تحويلها إلى WebP
          return $this->secureFileUploadService->securelyStoreFile($file, 'images/articles', true);
      } catch (\Exception $e) {
          // تسجيل الخطأ
          \Illuminate\Support\Facades\Log::error('فشل في تخزين صورة المقال: ' . $e->getMessage());
          
          // إرجاع صورة افتراضية في حالة الفشل
          return 'default.webp';
      }
  }
}
