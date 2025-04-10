<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = User::query();

            // البحث
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('email', 'like', "%{$searchTerm}%");
                });
            }

            // الترتيب
            $sortField = $request->get('sort_field', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);

            // التصفية حسب الدور
            if ($request->has('role')) {
                $query->role($request->role);
            }

            $users = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'status' => true,
                'message' => null,
                'data' => [
                    'users' => $users->map(function($user) {
                        return [
                            'id' => (int)$user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'avatar' => $user->avatar,
                            'roles' => $user->roles->pluck('name'),
                            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                            'last_activity' => $user->last_activity ? date('Y-m-d H:i:s', strtotime($user->last_activity)) : null
                        ];
                    }),
                    'pagination' => [
                        'current_page' => (int)$users->currentPage(),
                        'last_page' => (int)$users->lastPage(),
                        'per_page' => (int)$users->perPage(),
                        'total' => (int)$users->total()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء جلب المستخدمين',
                'data' => null
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Password::defaults()],
                'roles' => ['required', 'array'],
                'roles.*' => ['exists:roles,name'],
                'avatar' => ['nullable', 'image', 'max:2048']
            ], [
                'name.required' => 'حقل الاسم مطلوب',
                'email.required' => 'حقل البريد الإلكتروني مطلوب',
                'email.email' => 'يجب أن يكون البريد الإلكتروني صالحاً',
                'email.unique' => 'البريد الإلكتروني مستخدم مسبقاً',
                'password.required' => 'حقل كلمة المرور مطلوب',
                'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
                'roles.required' => 'يجب تحديد دور واحد على الأقل'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'data' => [
                        'errors' => $validator->errors()
                    ]
                ], 422);
            }

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ];

            // معالجة الصورة الشخصية
            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('public/avatars');
                $userData['avatar'] = Storage::url($path);
            }

            $user = User::create($userData);
            $user->assignRole($request->roles);

            return response()->json([
                'status' => true,
                'message' => 'تم إنشاء المستخدم بنجاح',
                'data' => [
                    'user' => [
                        'id' => (int)$user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                        'roles' => $user->roles->pluck('name'),
                        'created_at' => $user->created_at->format('Y-m-d H:i:s')
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء إنشاء المستخدم',
                'data' => null
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            
            $userData = [
                'id' => (int)$user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'job_title' => $user->job_title ?? '',
                'gender' => $user->gender ?? '',
                'country' => $user->country ?? '',
                'bio' => $user->bio ?? '',
                'social_links' => $user->social_links ?? '',
                'avatar' => $user->profile_photo_url,
                'status' => $user->status ?? 'offline',
                'last_activity' => $user->last_activity ? $user->last_activity->format('Y-m-d H:i:s') : null,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $user->updated_at->format('Y-m-d H:i:s')
            ];

            return response()->json([
                'status' => true,
                'message' => null,
                'data' => [
                    'user' => $userData
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'المستخدم غير موجود',
                'data' => null
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء جلب بيانات المستخدم',
                'data' => null
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
                'password' => ['nullable', 'confirmed', Password::defaults()],
                'roles' => ['required', 'array'],
                'roles.*' => ['exists:roles,name'],
                'avatar' => ['nullable', 'image', 'max:2048']
            ], [
                'name.required' => 'حقل الاسم مطلوب',
                'email.required' => 'حقل البريد الإلكتروني مطلوب',
                'email.email' => 'يجب أن يكون البريد الإلكتروني صالحاً',
                'email.unique' => 'البريد الإلكتروني مستخدم مسبقاً',
                'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
                'roles.required' => 'يجب تحديد دور واحد على الأقل'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'data' => [
                        'errors' => $validator->errors()
                    ]
                ], 422);
            }

            $userData = [
                'name' => $request->name,
                'email' => $request->email
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            // معالجة الصورة الشخصية
            if ($request->hasFile('avatar')) {
                // حذف الصورة القديمة إذا كانت موجودة
                if ($user->avatar) {
                    Storage::delete(str_replace('/storage', 'public', $user->avatar));
                }
                
                $path = $request->file('avatar')->store('public/avatars');
                $userData['avatar'] = Storage::url($path);
            }

            $user->update($userData);
            $user->syncRoles($request->roles);

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث المستخدم بنجاح',
                'data' => [
                    'user' => [
                        'id' => (int)$user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                        'roles' => $user->roles->pluck('name'),
                        'created_at' => $user->created_at->format('Y-m-d H:i:s')
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء تحديث المستخدم',
                'data' => null
            ], 500);
        }
    }

    public function updateProfile(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
                'phone' => ['nullable', 'string', 'max:20'],
                'job_title' => ['nullable', 'string', 'max:100'],
                'gender' => ['nullable', 'string', 'in:male,female'],
                'country' => ['nullable', 'string', 'max:100'],
                'bio' => ['nullable', 'string', 'max:500'],
                'social_links' => ['nullable', 'string']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'data' => [
                        'errors' => $validator->errors()
                    ]
                ], 422);
            }

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'job_title' => $request->job_title,
                'gender' => $request->gender,
                'country' => $request->country,
                'bio' => $request->bio,
                'social_links' => $request->social_links
            ];

            $user->update($userData);

            // تحديث آخر نشاط للمستخدم
            $user->last_activity = now();
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث الملف الشخصي بنجاح',
                'data' => [
                    'user' => [
                        'id' => (int)$user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone ?? '',
                        'job_title' => $user->job_title ?? '',
                        'gender' => $user->gender ?? '',
                        'country' => $user->country ?? '',
                        'bio' => $user->bio ?? '',
                        'social_links' => $user->social_links ?? '',
                        'avatar' => $user->profile_photo_url,
                        'status' => $user->status ?? 'offline',
                        'last_activity' => $user->last_activity ? $user->last_activity->format('Y-m-d H:i:s') : null,
                        'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $user->updated_at->format('Y-m-d H:i:s')
                    ]
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'المستخدم غير موجود',
                'data' => null
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء تحديث الملف الشخصي',
                'data' => null
            ], 500);
        }
    }

    public function updateProfilePhoto(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'photo' => ['required', 'image', 'max:2048']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'data' => [
                        'errors' => $validator->errors()
                    ]
                ], 422);
            }

            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->profile_photo_path) {
                Storage::delete('public/' . $user->profile_photo_path);
            }
            
            // تخزين الصورة الجديدة
            $path = $request->file('photo')->store('public/profile-photos');
            $user->profile_photo_path = str_replace('public/', '', $path);
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث الصورة الشخصية بنجاح',
                'data' => [
                    'user' => [
                        'id' => (int)$user->id,
                        'avatar' => $user->profile_photo_url
                    ]
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'المستخدم غير موجود',
                'data' => null
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء تحديث الصورة الشخصية',
                'data' => null
            ], 500);
        }
    }
}
