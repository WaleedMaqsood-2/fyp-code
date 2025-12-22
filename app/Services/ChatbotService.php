<?php

namespace App\Services;

class ChatbotService
{
    private $responses = [
        // General FAQs
        'شکایت کیسے درج کریں' => [
            'answer' => 'آپ شکایت درج کرنے کے لیے درج ذیل طریقے استعمال کر سکتے ہیں:
1. **آواز ریکارڈنگ**: مائیکروفون کے بٹن پر کلک کریں اور اپنی شکایت ریکارڈ کریں
2. **تحریری فارم**: تفصیلات باکس میں اپنی شکایت لکھیں
3. **فائل اپلوڈ**: متعلقہ ثبوت کی فائلیں اپلوڈ کریں',
            'related' => ['درج', 'شکایت', 'جمع']
        ],
        
        'ٹریک آئی ڈی کیا ہے' => [
            'answer' => '**ٹریک آئی ڈی** ایک منفرد نمبر ہے جو آپ کی شکایت درج کرتے وقت آپ کو دیا جاتا ہے۔ مثال: CT-2024-000123

اس کے ذریعے آپ:
• اپنی شکایت کا حال جان سکتے ہیں
• کیس کی صورت حال چیک کر سکتے ہیں
• اپ ڈیٹس حاصل کر سکتے ہیں

ٹریک آئی ڈی کو محفوظ رکھیں۔',
            'related' => ['ٹریک', 'نمبر', 'حال', 'صورت حال']
        ],
        
        'انیسومس رپورٹ' => [
            'answer' => 'آپ بنا اکاؤنٹ بنائے **گمنام شکایت** درج کر سکتے ہیں:

**فوائد:**
• کوئی ذاتی معلومات درکار نہیں
• آپ کی شناخت مخفی رہتی ہے
• شکایت کا جائزہ لیا جاتا ہے

**نوٹ:** گمنام شکایات پر کارروائی ہوتی ہے لیکن مزید معلومات کے لیے رابطہ مشکل ہو سکتا ہے۔',
            'related' => ['گمنام', 'بے نام', 'شناخت']
        ],
        
        'مدت' => [
            'answer' => 'شکایات کے حل کا وقت **قسم اور شدت** پر منحصر ہے:

**عام شکایات:** 7-10 کاروباری دن
**فوری معاملات:** 24-48 گھنٹے
**پیچیدہ معاملات:** 15-30 دن

آپ اپنی ٹریک آئی ڈی سے وقتاً فوقتاً صورت حال چیک کر سکتے ہیں۔',
            'related' => ['وقت', 'دن', 'حل', 'کارروائی']
        ],
        
        'ثبوت' => [
            'answer' => 'آپ درج ذیل **ثبوت** اپلوڈ کر سکتے ہیں:

**قبول فائلیں:**
• تصاویر (JPG, PNG) - واقعے کی تصاویر، زخموں کی تصاویر
• ویڈیوز (MP4, AVI) - سی سی ٹی وی فوٹیج، موبائل ویڈیوز
• آڈیوز (MP3, WAV) - آواز کی ریکارڈنگ، بات چیت
• دستاویزات (PDF, DOC) - رپورٹس، دستاویزات

**سائز:** زیادہ سے زیادہ 10MB فی فائل',
            'related' => ['فائل', 'اپلوڈ', 'تصویر', 'ویڈیو']
        ],
        
        'رجسٹر' => [
            'answer' => 'اکاؤنٹ بنانے کے **فوائد:**
• تمام شکایات ایک جگہ
• فوری اطلاعیں
• کیس ہسٹری
• ترجیحی سلوک

**مراحل:**
1. "رجسٹر" پر کلک کریں
2. ای میل اور پاس ورڈ ڈالیں
3. تصدیقی میل چیک کریں
4. لاگ ان کریں',
            'related' => ['اکاؤنٹ', 'سائن اپ', 'لاگ ان']
        ],
        
        'پولیس اسٹیشن' => [
            'answer' => 'اگر آپ براہ راست **پولیس اسٹیشن** جانا چاہتے ہیں تو:

**قریبی اسٹیشنز تلاش کریں:**
1. ہوم پیج پر "پولیس اسٹیشنز" پر کلک کریں
2. اپنا علاقہ درج کریں
3. قریبی اسٹیشنز کی فہرست ملے گی
4. پتہ، فون نمبر اور دورانیہ دیکھیں

**ہنگامی صورت:** 15 پر کال کریں',
            'related' => ['اسٹیشن', 'علاقہ', 'قریب']
        ],
        
        'فون نمبر' => [
            'answer' => '**رابطے کے نمبر:**

ہنگامی: **15**
شہری ہیلپ لائن: **111-222-333**
سائبر کرائم: **111-444-555**
خواتین ہیلپ لائن: **111-666-777**

**ای میل:** help@policeportal.gov.pk
**واٹس ایپ:** 0300-1234567',
            'related' => ['رابطہ', 'کال', 'ہیلپ', 'ای میل']
        ]
    ];

    // Get response for user query
    public function getResponse($userQuery)
    {
        // Clean and normalize query
        $query = $this->cleanQuery($userQuery);
        
        // Try exact match first
        foreach ($this->responses as $question => $data) {
            if (strpos($query, $question) !== false) {
                return [
                    'answer' => $data['answer'],
                    'confidence' => 1.0,
                    'matched_question' => $question
                ];
            }
        }
        
        // Try related keywords
        $bestMatch = $this->findByKeywords($query);
        if ($bestMatch) {
            return $bestMatch;
        }
        
        // Default response
        return [
            'answer' => 'معذرت، میں آپ کے سوال کا جواب نہیں دے سکتا۔ براہ کرم درج ذیل میں سے کوئی ایک سوال پوچھیں:
• شکایت کیسے درج کریں؟
• ٹریک آئی ڈی کیا ہے؟
• گمنام رپورٹ کیسے کریں؟
• شکایت کا حل کتنا وقت لگے گا؟
• کس قسم کے ثبوت اپلوڈ کر سکتے ہیں؟',
            'confidence' => 0.0,
            'suggested_questions' => array_keys($this->responses)
        ];
    }
    
    // Clean user query
    private function cleanQuery($query)
    {
        // Remove extra spaces
        $query = trim(preg_replace('/\s+/', ' ', $query));
        
        // Convert to Urdu friendly
        $query = str_replace(['?', '!', '.', ','], '', $query);
        
        return $query;
    }
    
    // Find response by keywords
    private function findByKeywords($query)
    {
        $bestScore = 0;
        $bestResponse = null;
        
        foreach ($this->responses as $question => $data) {
            $score = 0;
            
            // Check main question
            if (strpos($query, $question) !== false) {
                $score += 10;
            }
            
            // Check related keywords
            foreach ($data['related'] as $keyword) {
                if (strpos($query, $keyword) !== false) {
                    $score += 5;
                }
            }
            
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestResponse = [
                    'answer' => $data['answer'],
                    'confidence' => min(1.0, $score / 20),
                    'matched_keywords' => $data['related']
                ];
            }
        }
        
        return $bestScore > 5 ? $bestResponse : null;
    }
    
    // Get all FAQ questions for display
    public function getAllQuestions()
    {
        return array_keys($this->responses);
    }
    
    // Get suggested questions based on query
    public function getSuggestions($partialQuery)
    {
        $suggestions = [];
        
        foreach ($this->responses as $question => $data) {
            // Check if question contains partial query
            if (strpos($question, $partialQuery) !== false || 
                $this->checkKeywords($partialQuery, $data['related'])) {
                $suggestions[] = $question;
            }
            
            if (count($suggestions) >= 5) {
                break;
            }
        }
        
        return $suggestions;
    }
    
    private function checkKeywords($query, $keywords)
    {
        foreach ($keywords as $keyword) {
            if (strpos($query, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
}