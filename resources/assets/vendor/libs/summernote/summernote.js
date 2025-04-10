import $ from 'jquery';
import 'bootstrap';

// Import Summernote
import 'summernote';

// Set jQuery globally
window.jQuery = $;
window.$ = $;

import axios from 'axios';
window.axios = axios;
// Initialize Summernote language
$.extend($.summernote.lang, {
  'ar-AR': {
    font: {
      bold: 'عريض',
      italic: 'مائل',
      underline: 'تحته خط',
      clear: 'مسح التنسيق',
      height: 'إرتفاع السطر',
      name: 'الخط',
      strikethrough: 'فى وسطه خط',
      size: 'الحجم'
    },
    image: {
      image: 'صورة',
      insert: 'إضافة صورة',
      resizeFull: 'الحجم بالكامل',
      resizeHalf: 'تصغير للنصف',
      resizeQuarter: 'تصغير للربع',
      floatLeft: 'تطويف لليسار',
      floatRight: 'تطويف لليمين',
      floatNone: 'ثابته',
      dragImageHere: 'إدرج الصورة هنا',
      selectFromFiles: 'حدد ملف',
      url: 'رابط الصورة',
      remove: 'حذف الصورة'
    },
    link: {
      link: 'رابط',
      insert: 'إدراج',
      unlink: 'حذف الرابط',
      edit: 'تعديل',
      textToDisplay: 'النص',
      url: 'مسار الرابط',
      openInNewWindow: 'فتح في نافذة جديدة'
    },
    table: {
      table: 'جدول'
    },
    hr: {
      insert: 'إدراج خط أفقي'
    },
    style: {
      style: 'تنسيق',
      p: 'عادي',
      blockquote: 'إقتباس',
      pre: 'شفرة',
      h1: 'عنوان رئيسي 1',
      h2: 'عنوان رئيسي 2',
      h3: 'عنوان رئيسي 3',
      h4: 'عنوان رئيسي 4',
      h5: 'عنوان رئيسي 5',
      h6: 'عنوان رئيسي 6'
    },
    lists: {
      unordered: 'قائمة مُنقطة',
      ordered: 'قائمة مُرقمة'
    },
    options: {
      help: 'مساعدة',
      fullscreen: 'حجم الشاشة بالكامل',
      codeview: 'شفرة المصدر'
    },
    paragraph: {
      paragraph: 'فقرة',
      outdent: 'محاذاة للخارج',
      indent: 'محاذاة للداخل',
      left: 'محاذاة لليسار',
      center: 'توسيط',
      right: 'محاذاة لليمين',
      justify: 'ضبط'
    },
    color: {
      recent: 'تم إستخدامه',
      more: 'المزيد',
      background: 'لون الخلفية',
      foreground: 'لون النص',
      transparent: 'شفاف',
      setTransparent: 'بدون خلفية',
      reset: 'إعادة الضبط',
      resetToDefault: 'إعادة الضبط'
    }
  }
});
