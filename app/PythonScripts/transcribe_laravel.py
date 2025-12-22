# D:\web development\laravel\FYP\app\PythonScripts\transcribe_laravel.py

import sys
import os

# ============================================
# FIX FOR LARAVEL ENVIRONMENT
# ============================================

# Windows-specific fixes
if sys.platform == 'win32':
    # Set environment variables for Laravel
    os.environ["KMP_DUPLICATE_LIB_OK"] = "TRUE"
    os.environ["NO_PROXY"] = "*"
    os.environ["HTTP_PROXY"] = ""
    os.environ["HTTPS_PROXY"] = ""
    
    # Fix for asyncio in Laravel environment
    try:
        import asyncio
        asyncio.set_event_loop_policy(asyncio.WindowsSelectorEventLoopPolicy())
    except:
        pass

def main():
    try:
        # Get arguments
        if len(sys.argv) < 2:
            print("فائل پتہ درکار ہے۔||File path required.||0.1")
            return
        
        audio_path = sys.argv[1]
        language = sys.argv[2] if len(sys.argv) > 2 else "ur"
        
        print(f"Processing: {audio_path}", file=sys.stderr)
        
        if not os.path.exists(audio_path):
            print("فائل نہیں ملی۔||File not found.||0.1")
            return
        
        # Try to import whisper with error handling
        try:
            print("Importing whisper...", file=sys.stderr)
            import whisper
            print("Whisper imported successfully", file=sys.stderr)
        except ImportError as e:
            print(f"Whisper import error: {e}", file=sys.stderr)
            print("وہسپر ماڈل نہیں ملا۔||Whisper model nahi mila.||0.2")
            return
        
        # Set model cache directory
        cache_dir = os.path.join(os.path.expanduser('~'), '.cache', 'whisper')
        os.makedirs(cache_dir, exist_ok=True)
        
        print(f"Cache directory: {cache_dir}", file=sys.stderr)
        
        # Try to load model
        try:
            print("Loading model...", file=sys.stderr)
            
            # First try tiny model
            try:
                model = whisper.load_model("tiny", download_root=cache_dir)
                print("Tiny model loaded", file=sys.stderr)
            except Exception as e1:
                print(f"Tiny failed: {e1}", file=sys.stderr)
                # Try base model
                try:
                    model = whisper.load_model("base", download_root=cache_dir)
                    print("Base model loaded", file=sys.stderr)
                except Exception as e2:
                    print(f"Base failed: {e2}", file=sys.stderr)
                    raise Exception("All models failed to load")
            
        except Exception as e:
            print(f"Model load error: {e}", file=sys.stderr)
            print("ماڈل لوڈ نہیں ہو سکا۔||Model load nahi ho saka.||0.3")
            return
        
        # Transcribe
        try:
            print("Transcribing...", file=sys.stderr)
            result = model.transcribe(
                audio_path,
                language=language,
                task="transcribe",
                fp16=False,
                verbose=False
            )
            
            text = result.get('text', '').strip()
            print(f"Text extracted: {len(text)} chars", file=sys.stderr)
            
            if text:
                # Simple roman conversion
                roman_text = convert_to_roman(text)
                confidence = 0.8
                
                # Calculate confidence if available
                if 'segments' in result and result['segments']:
                    try:
                        confidences = [s.get('confidence', 0.5) for s in result['segments']]
                        confidence = sum(confidences) / len(confidences)
                    except:
                        confidence = 0.8
                
                output = f"{text}||{roman_text}||{confidence:.2f}"
                print(output)
            else:
                print("آڈیو میں کوئی واضح آواز نہیں ملی۔||Audio mein koi wazeh awaz nahi mili.||0.5")
                
        except Exception as e:
            print(f"Transcription error: {e}", file=sys.stderr)
            print("ٹرانککرپشن ناکام۔||Transcription nakam.||0.4")
            
    except Exception as e:
        print(f"Unexpected error: {e}", file=sys.stderr)
        print("سسٹم میں غیر متوقع خرابی۔||System mein ghair mutawaqqa kharabi.||0.1")

def convert_to_roman(text):
    """Simple Urdu to Roman conversion"""
    if not text:
        return ""
    
    mapping = {
        'ا': 'a', 'آ': 'aa', 'ب': 'b', 'پ': 'p', 'ت': 't',
        'ٹ': 'tt', 'ث': 's', 'ج': 'j', 'چ': 'ch', 'ح': 'h',
        'خ': 'kh', 'د': 'd', 'ڈ': 'dd', 'ذ': 'z', 'ر': 'r',
        'ڑ': 'rr', 'ز': 'z', 'ژ': 'zh', 'س': 's', 'ش': 'sh',
        'ص': 's', 'ض': 'z', 'ط': 't', 'ظ': 'z', 'ع': 'a',
        'غ': 'gh', 'ف': 'f', 'ق': 'q', 'ک': 'k', 'گ': 'g',
        'ل': 'l', 'م': 'm', 'ن': 'n', 'و': 'w', 'ہ': 'h',
        'ھ': 'h', 'ی': 'y', 'ے': 'e', 'ں': 'n',
        ' ': ' ', '.': '.', ',': ',', '؟': '?', '۔': '.',
        '!': '!', ':': ':', '؛': ';', '(': '(', ')': ')'
    }
    
    result = []
    for char in text:
        result.append(mapping.get(char, char))
    
    return ''.join(result)

if __name__ == "__main__":
    main()