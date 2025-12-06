# D:\web development\laravel\FYP\app\PythonScripts\transcribe_final.py

import sys
import os

# ============================================
# WINDOWS FIX - Use raw strings for Windows paths
# ============================================
if sys.platform == 'win32':
    # Disable asyncio imports
    import builtins
    original_import = builtins.__import__
    
    def custom_import(name, *args, **kwargs):
        if name == 'asyncio' or name.startswith('asyncio.'):
            # Return a dummy module
            class DummyModule:
                def __getattr__(self, name):
                    return DummyModule()
                def __call__(self, *args, **kwargs):
                    return None
            return DummyModule()
        return original_import(name, *args, **kwargs)
    
    builtins.__import__ = custom_import

# Environment variables
os.environ["KMP_DUPLICATE_LIB_OK"] = "TRUE"
os.environ["NO_PROXY"] = "*"
os.environ["HTTP_PROXY"] = ""
os.environ["HTTPS_PROXY"] = ""

def main():
    try:
        # Set UTF-8 encoding
        if hasattr(sys.stdout, 'reconfigure'):
            sys.stdout.reconfigure(encoding='utf-8')
            sys.stderr.reconfigure(encoding='utf-8')
        
        print("=== TRANSCRIPTION SCRIPT STARTED ===", file=sys.stderr)
        
        if len(sys.argv) < 2:
            print("ERROR: Audio path required", file=sys.stderr)
            sys.exit(1)
        
        audio_path = sys.argv[1]
        language = sys.argv[2] if len(sys.argv) > 2 else "ur"
        
        print(f"DEBUG: Audio path: {audio_path}", file=sys.stderr)
        print(f"DEBUG: Language: {language}", file=sys.stderr)
        print(f"DEBUG: File exists: {os.path.exists(audio_path)}", file=sys.stderr)
        
        if not os.path.exists(audio_path):
            print("ERROR: File does not exist", file=sys.stderr)
            print("فائل نہیں ملی۔||File nahi mili.||0.1")
            return
        
        # Try to import whisper
        try:
            print("DEBUG: Importing whisper...", file=sys.stderr)
            import whisper
            print("DEBUG: Whisper imported successfully", file=sys.stderr)
        except ImportError as e:
            print(f"ERROR: Cannot import whisper: {e}", file=sys.stderr)
            print("وہسپر ماڈل نہیں ملا۔||Whisper model nahi mila.||0.2")
            return
        
        # Define model paths with raw strings (r'') for Windows
        model_paths = [
            r'C:\Users\PMLS\.cache\whisper\tiny.pt',  # Primary
            r'D:\.cache\whisper\tiny.pt',             # Alternative
            os.path.join(os.path.expanduser('~'), '.cache', 'whisper', 'tiny.pt')  # Default
        ]
        
        # Check which model exists
        model_to_use = None
        for model_path in model_paths:
            if os.path.exists(model_path):
                model_to_use = model_path
                print(f"DEBUG: Found model at: {model_path}", file=sys.stderr)
                break
        
        if not model_to_use:
            print("DEBUG: No local model found, trying to download...", file=sys.stderr)
            model_to_use = 'tiny'  # Will try to download
        
        # Load model
        try:
            print(f"DEBUG: Loading model: {model_to_use}", file=sys.stderr)
            
            if isinstance(model_to_use, str) and model_to_use.endswith('.pt'):
                # Load from local file
                import torch
                from whisper import load_model
                
                # Load base model
                model = load_model('tiny')
                # Load weights from local file
                model_state = torch.load(model_to_use, map_location='cpu')
                model.load_state_dict(model_state)
                print("DEBUG: Model loaded from local file", file=sys.stderr)
            else:
                # Try to download (may fail without internet)
                model = whisper.load_model(model_to_use)
                print(f"DEBUG: Model '{model_to_use}' loaded", file=sys.stderr)
                
        except Exception as e:
            print(f"ERROR: Model load failed: {e}", file=sys.stderr)
            print("ماڈل لوڈ نہیں ہو سکا۔||Model load nahi ho saka.||0.3")
            return
        
        # Transcribe
        try:
            print("DEBUG: Starting transcription...", file=sys.stderr)
            result = model.transcribe(
                audio_path,
                language=language,
                task="transcribe",
                fp16=False,
                verbose=False
            )
            print("DEBUG: Transcription completed", file=sys.stderr)
            
            text = result.get('text', '').strip()
            print(f"DEBUG: Extracted text length: {len(text)}", file=sys.stderr)
            
            if text:
                print(f"DEBUG: First 100 chars: {text[:100]}", file=sys.stderr)
            else:
                print("DEBUG: No text found", file=sys.stderr)
                
        except Exception as e:
            print(f"ERROR: Transcription failed: {e}", file=sys.stderr)
            print("ٹرانککرپشن ناکام۔||Transcription nakam.||0.4")
            return
        
        # Prepare output
        if not text:
            text = "آڈیو میں کوئی واضح آواز نہیں ملی۔"
        
        # Simple roman conversion
        roman_text = convert_to_roman(text)
        
        # Confidence
        confidence = 0.8
        if result and 'segments' in result and result['segments']:
            try:
                confidences = [s.get('confidence', 0.5) for s in result['segments']]
                confidence = sum(confidences) / len(confidences)
            except:
                confidence = 0.8
        
        # Output
        output = f"{text}||{roman_text}||{confidence:.2f}"
        print(f"DEBUG: Final output prepared", file=sys.stderr)
        print("=== TRANSCRIPTION COMPLETED SUCCESSFULLY ===", file=sys.stderr)
        print(output)
        
    except Exception as e:
        print(f"CRITICAL: Unexpected error: {e}", file=sys.stderr)
        import traceback
        traceback.print_exc(file=sys.stderr)
        print("سسٹم میں غیر متوقع خرابی۔||System mein ghair mutawaqqa kharabi.||0.1")

def convert_to_roman(text):
    """Simple Urdu to Roman conversion"""
    if not text:
        return ""
    
    # Basic mapping
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
        '!': '!', ':': ':', '؛': ';', '(': '(', ')': ')',
        '\n': ' ', '\r': ' ', '\t': ' '
    }
    
    result = []
    for char in text:
        result.append(mapping.get(char, char))
    
    return ''.join(result)

if __name__ == "__main__":
    main()