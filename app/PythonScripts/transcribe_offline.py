import sys
import whisper
import os

def romanize_text(urdu_text):
    mapping = {
        'ا':'a','آ':'aa','ب':'b','پ':'p','ت':'t','ٹ':'tt','ث':'s','ج':'j','چ':'ch',
        'ح':'h','خ':'kh','د':'d','ڈ':'dd','ذ':'z','ر':'r','ڑ':'rr','ز':'z','ژ':'zh',
        'س':'s','ش':'sh','ص':'s','ض':'z','ط':'t','ظ':'z','ع':'a','غ':'gh','ف':'f',
        'ق':'q','ک':'k','گ':'g','ل':'l','م':'m','ن':'n','و':'w','ہ':'h','ھ':'h','ی':'y',
        'ے':'e','ں':'n',' ':' ','،':',','۔':'.','؟':'?','!':'!','(':'(',')':')',':':':','؛':';'
    }
    return ''.join(mapping.get(c, c) for c in urdu_text)

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Usage: python transcribe_offline.py <audioPath> <lang>")
        sys.exit(1)

    audio_path = sys.argv[1]
    lang = sys.argv[2]

    if not os.path.exists(audio_path):
        print("Error: file not found")
        sys.exit(1)

    model = whisper.load_model("small")  # tiny, base, small, medium, large
    result = model.transcribe(audio_path, language=lang)

    urdu_text = result["text"].strip()
    roman_text = romanize_text(urdu_text)
    confidence = round(result["segments"][0]["avg_logprob"],2) if "segments" in result else 0.5

    print(f"{urdu_text}||{roman_text}||{confidence}")
