<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class FrontController extends Controller
{
    public function home()
    {
        return view('content.front.home');
    }

    public function aboutUs()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('content.front.about-us', compact('settings'));
    }

    public function contactUs()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('content.front.contact-us', compact('settings'));
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            $settings = Setting::pluck('value', 'key')->toArray();

            // Get email from settings or use default
            $contactEmail = $settings['contact_email'] ?? 'info@alemedu.com';

            // Send email
            Mail::to($contactEmail)->send(new ContactFormMail($validated));

            // Log success
            Log::info('Contact form submitted successfully', [
                'from' => $validated['email'],
                'to' => $contactEmail,
                'subject' => $validated['subject']
            ]);

            return back()->with('success', __('تم إرسال رسالتك بنجاح! سنقوم بالرد عليك في أقرب وقت ممكن.'));
        } catch (\Exception $e) {
            // Log error
            Log::error('Error sending contact form email', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);

            return back()
                ->withInput()
                ->with('error', __('عذراً، حدث خطأ أثناء إرسال رسالتك. الرجاء المحاولة مرة أخرى لاحقاً.'));
        }
    }
}
