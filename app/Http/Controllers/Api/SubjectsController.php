<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiDatabaseTrait;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{
    use ApiDatabaseTrait;

    public function index(Request $request, $country)
    {
        // تبديل قاعدة البيانات
        $this->switchDatabase($country);

        $subjects = Subject::with('semesters')->get();
        
        return $this->getResponseWithDatabase($subjects);
    }

    public function show(Request $request, $country, $id)
    {
        // تبديل قاعدة البيانات
        $this->switchDatabase($country);

        $subject = Subject::with(['content', 'materials'])->find($id);
        
        if (!$subject) {
            return $this->getResponseWithDatabase(
                null,
                false,
                'المادة غير موجودة'
            );
        }

        return $this->getResponseWithDatabase($subject);
    }
}
