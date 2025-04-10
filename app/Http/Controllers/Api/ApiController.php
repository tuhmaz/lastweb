<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\News;
use App\Models\Subject;
use App\Models\Comment;
use App\Models\Message;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    // Authentication Methods
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات الدخول غير صحيحة'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|unique:users'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء الحساب بنجاح',
            'user' => $user,
            'token' => $token
        ]);
    }

    // News Methods
    public function getNews(Request $request)
    {
        $news = News::with(['category', 'comments'])
            ->when($request->category_id, function($query, $category_id) {
                return $query->where('category_id', $category_id);
            })
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => true,
            'news' => $news
        ]);
    }

    public function getNewsDetails($id)
    {
        $news = News::with(['category', 'comments.user'])->find($id);
        
        if (!$news) {
            return response()->json([
                'status' => false,
                'message' => 'الخبر غير موجود'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'news' => $news
        ]);
    }

    // Subjects Methods
    public function getSubjects()
    {
        $subjects = Subject::with('semesters')->get();
        
        return response()->json([
            'status' => true,
            'subjects' => $subjects
        ]);
    }

    public function getSubjectContent($id)
    {
        $subject = Subject::with(['content', 'materials'])->find($id);
        
        if (!$subject) {
            return response()->json([
                'status' => false,
                'message' => 'المادة غير موجودة'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'subject' => $subject
        ]);
    }

    // Comments Methods
    public function addComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'news_id' => 'required|exists:news,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        $comment = Comment::create([
            'content' => $request->content,
            'news_id' => $request->news_id,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم إضافة التعليق بنجاح',
            'comment' => $comment->load('user')
        ]);
    }

    // Messages Methods
    public function getMessages()
    {
        $messages = Message::where('user_id', auth()->id())
            ->orWhere('receiver_id', auth()->id())
            ->with(['user', 'receiver'])
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'messages' => $messages
        ]);
    }

    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'receiver_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        $message = Message::create([
            'content' => $request->content,
            'user_id' => auth()->id(),
            'receiver_id' => $request->receiver_id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم إرسال الرسالة بنجاح',
            'data' => $message->load(['user', 'receiver'])
        ]);
    }

    // Profile Methods
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . auth()->id(),
            'phone' => 'string|unique:users,phone,' . auth()->id(),
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = Str::random(20) . '.' . $avatar->getClientOriginalExtension();
            $avatar->storeAs('public/avatars', $filename);
            $user->avatar = $filename;
        }

        $user->fill($request->only(['name', 'email', 'phone']));
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'user' => $user
        ]);
    }

    // Categories Methods
    public function getCategories()
    {
        $categories = Category::withCount('news')->get();
        
        return response()->json([
            'status' => true,
            'categories' => $categories
        ]);
    }
}
