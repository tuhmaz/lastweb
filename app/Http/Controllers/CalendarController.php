<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function index()
    {
        // الحصول على قائمة قواعد البيانات المتاحة
        $databases = config('database.connections');
        $availableDatabases = collect($databases)
            ->filter(function ($config, $name) {
                return in_array($name, [
                    'mysql', 'jo', 'sa', 'eg', 'ps'
                ]); // قائمة قواعد البيانات المسموح بها
            })
            ->keys()
            ->toArray();

        return view('content.dashboard.calendar.index', [
            'databases' => $availableDatabases,
            'currentDatabase' => session('database', config('database.default'))
        ]);
    }

    public function getEvents(Request $request)
    {
        try {
            $database = $request->input('database', session('database', config('database.default')));

            \Log::info('Fetching Events:', [
                'database' => $database,
                'timestamp' => now()->toDateTimeString()
            ]);

            DB::setDefaultConnection($database);

            $events = Event::on($database)
                ->select([
                    'id',
                    'title',
                    'description',
                    'event_date'
                ])
                ->orderBy('event_date', 'asc')
                ->get()
                ->map(function ($event) use ($database) {
                    // تسجيل البيانات الأصلية
                    \Log::debug('Raw Event Data:', [
                        'id' => $event->id,
                        'title' => $event->title,
                        'description' => $event->description,
                        'event_date' => $event->event_date
                    ]);

                    // تحويل البيانات لتناسب FullCalendar
                    $eventData = [
                      'id' => $event->id,
                      'title' => $event->title,
                      'start' => $event->event_date,
                      'allDay' => true,
                      'extendedProps' => [
                          'description' => $event->description ?: 'لا يوجد وصف', // إضافة قيمة افتراضية
                          'database' => $database
                      ]
                  ];

                    // تسجيل البيانات المحولة
                    \Log::debug('Transformed Event Data:', $eventData);

                    return $eventData;
                });

            \Log::info('Events Fetched Successfully', [
                'count' => $events->count(),
                'sample_event' => $events->first()
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $events
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');

        } catch (\Exception $e) {
            \Log::error('Calendar Events Error:', [
                'message' => $e->getMessage(),
                'database' => $database ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء جلب الأحداث'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // تسجيل البيانات الواردة
            \Log::info('Event Creation Request:', [
                'request_data' => $request->all()
            ]);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'event_date' => 'required|date',
                'eventDatabase' => 'required|string'
            ]);

            // تسجيل البيانات بعد التحقق
            \Log::info('Validated Event Data:', [
                'validated_data' => $validated
            ]);

            // استخدام قاعدة البيانات المحددة
            $database = $validated['eventDatabase'];
            DB::setDefaultConnection($database);

            // تسجيل معلومات قاعدة البيانات
            \Log::info('Database Connection:', [
                'database' => $database,
                'default_connection' => DB::getDefaultConnection()
            ]);

            // تحويل التاريخ إلى التنسيق الصحيح
            $eventDate = Carbon::parse($validated['event_date'])->format('Y-m-d');

            // تسجيل معلومات التاريخ
            \Log::info('Event Date Processing:', [
                'original_date' => $validated['event_date'],
                'formatted_date' => $eventDate
            ]);

            // إنشاء الحدث
            $event = new Event();
            $event->setConnection($database);
            $event->title = $validated['title'];
            $event->description = $validated['description'];
            $event->event_date = $eventDate;

            // تسجيل بيانات الحدث قبل الحفظ
            \Log::info('Event Object Before Save:', [
                'event_data' => $event->toArray()
            ]);

            $event->save();

            // تسجيل نجاح العملية
            \Log::info('Event Created Successfully:', [
                'event_id' => $event->id,
                'event_data' => $event->toArray()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Event created successfully',
                'data' => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $eventDate,
                    'end' => Carbon::parse($eventDate)->addHours(1)->format('Y-m-d H:i:s'),
                    'allDay' => true,
                    'extendedProps' => [
                        'description' => $event->description ?? '',
                        'database' => $database
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Event Creation Error:', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'database' => $database ?? 'unknown',
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create event: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'event_date' => 'required|date',
                'eventDatabase' => 'required|string'
            ]);

            // استخدام قاعدة البيانات المحددة
            $database = $validated['eventDatabase'];
            DB::setDefaultConnection($database);

            // تحويل التاريخ إلى التنسيق الصحيح
            $eventDate = Carbon::parse($validated['event_date'])->format('Y-m-d');

            // العثور على الحدث وتحديثه
            $event = Event::on($database)->findOrFail($id);
            $event->title = $validated['title'];
            $event->description = $validated['description'];
            $event->event_date = $eventDate;
            $event->save();

            \Log::info('Event Updated Successfully:', [
                'event_id' => $event->id,
                'event_data' => $event->toArray(),
                'database' => $database
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Event updated successfully',
                'data' => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $eventDate,
                    'end' => Carbon::parse($eventDate)->addHours(1)->format('Y-m-d H:i:s'),
                    'allDay' => true,
                    'extendedProps' => [
                        'description' => $event->description ?? '',
                        'database' => $database
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Event Update Error:', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'database' => $database ?? 'unknown',
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update event: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            // التحقق من وجود قاعدة البيانات
            if (!$request->has('database')) {
                throw new \Exception('Database parameter is required');
            }

            $database = $request->input('database');

            // تغيير اتصال قاعدة البيانات
            DB::setDefaultConnection($database);

            // البحث عن الحدث وحذفه
            $event = Event::on($database)->findOrFail($id);

            // تسجيل معلومات الحدث قبل الحذف
            \Log::info('Deleting Event:', [
                'event_id' => $id,
                'event_data' => $event->toArray(),
                'database' => $database
            ]);

            $event->delete();

            // تسجيل نجاح العملية
            \Log::info('Event Deleted Successfully:', [
                'event_id' => $id,
                'database' => $database
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف الحدث بنجاح'
            ]);
        } catch (\Exception $e) {
            \Log::error('Event Deletion Error:', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'database' => $database ?? 'unknown',
                'event_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'فشل في حذف الحدث: ' . $e->getMessage()
            ], 500);
        }
    }
}
