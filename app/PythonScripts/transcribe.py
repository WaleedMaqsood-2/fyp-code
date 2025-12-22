# #!/usr/bin/env python3
# """
# transcribe.py (Optimized v2)
# Usage:
#     python transcribe.py <audio_path> <lang> [--json] [--no-roman]

# Output (default):
#     <urdu_text>||<roman_text>||<confidence>

# If --json provided:
#     {"urdu_text": "...", "roman_text": "...", "confidence": 0.83, "raw": "..."}
# """

# import os
# import sys
# import argparse
# import json
# import math
# import traceback

# # ---- Windows / CPU safety ----
# # Force CPU usage (hide CUDA devices) and reduce thread usage to avoid Windows async issues.
# os.environ.setdefault("CUDA_VISIBLE_DEVICES", "")   # force CPU
# os.environ.setdefault("TRANSFORMERS_NO_ADVISORY_WARNINGS", "1")

# try:
#     # import torch carefully and set thread limits early
#     import torch
#     torch.set_num_threads(1)
#     torch.set_num_interop_threads(1)
# except Exception:
#     # We'll give a nicer error later if import fails
#     torch = None

# # import whisper after setting environment and torch threads
# try:
#     import whisper
# except Exception as e:
#     # Print helpful debug for Laravel to capture and display
#     print(json.dumps({
#         "error": "whisper_import_failed",
#         "message": str(e),
#         "traceback": traceback.format_exc()
#     }))
#     sys.exit(1)

# # Romanizer: try transformers transliteration model; otherwise fallback to unidecode.
# try:
#     from transformers import AutoTokenizer, AutoModelForSeq2SeqLM
#     TRANSFORMERS_OK = True
# except Exception:
#     TRANSFORMERS_OK = False

# try:
#     from unidecode import unidecode
# except Exception:
#     # very small fallback if not installed
#     def unidecode(s):
#         return ''.join(ch if ord(ch) < 128 else '?' for ch in s)

# # ---------- Helper functions ----------
# _transformer_model = None
# _transformer_tokenizer = None
# _whisper_model = None

# def load_whisper_model(size="small", device=None):
#     global _whisper_model
#     if _whisper_model is None:
#         # whisper uses torch; let it pick CPU from env
#         _whisper_model = whisper.load_model(size)
#     return _whisper_model

# def load_transformer_model(model_name="ai4bharat/IndicTrans2-en-ur"):
#     global _transformer_model, _transformer_tokenizer
#     if not TRANSFORMERS_OK:
#         return None, None
#     global _transformer_model, _transformer_tokenizer
#     if _transformer_model is None or _transformer_tokenizer is None:
#         try:
#             _transformer_tokenizer = AutoTokenizer.from_pretrained(model_name)
#             _transformer_model = AutoModelForSeq2SeqLM.from_pretrained(model_name)
#         except Exception:
#             _transformer_model = None
#             _transformer_tokenizer = None
#     return _transformer_model, _transformer_tokenizer

# def romanize_text(urdu_text):
#     """
#     Try transformers transliteration model first.
#     If that fails, fallback to unidecode (ASCII approximation).
#     """
#     if not urdu_text:
#         return ""
#     # prefer transformer if available
#     if TRANSFORMERS_OK:
#         try:
#             model, tokenizer = load_transformer_model()
#             if model and tokenizer:
#                 inputs = tokenizer(urdu_text, return_tensors="pt", truncation=True)
#                 # generate with limited length
#                 out = model.generate(**inputs, max_new_tokens=200)
#                 cand = tokenizer.decode(out[0], skip_special_tokens=True)
#                 if cand and len(cand.strip()) > 0:
#                     return cand.strip()
#         except Exception:
#             # fall through to unidecode fallback
#             pass

#     # fallback: ASCII approximation
#     try:
#         approx = unidecode(urdu_text)
#         return approx
#     except Exception:
#         return ""

# def compute_confidence(result):
#     """
#     Compute a confidence score from whisper result.
#     We'll use mean(avg_logprob) across segments if available and convert to a 0..1-ish scale.
#     If avg_logprob is in log-prob space, it can be negative; we'll approximate:
#         conf = sigmoid(mean_avg_logprob)
#     Keep final value between 0.0 and 1.0 and round to 2 decimals.
#     """
#     try:
#         segments = result.get("segments", [])
#         if not segments:
#             return 0.50
#         vals = []
#         for seg in segments:
#             if "avg_logprob" in seg and seg["avg_logprob"] is not None:
#                 vals.append(float(seg["avg_logprob"]))
#         if not vals:
#             return 0.50
#         mean_lp = sum(vals) / len(vals)
#         # numerical stable sigmoid
#         try:
#             conf = 1.0 / (1.0 + math.exp(-mean_lp))
#         except OverflowError:
#             conf = 0.0 if mean_lp < 0 else 1.0
#         # clamp
#         conf = max(0.0, min(1.0, conf))
#         return round(conf, 2)
#     except Exception:
#         return 0.50

# # ---------- Main ----------
# def main():
#     parser = argparse.ArgumentParser(description="Transcribe audio -> Urdu||Roman||Confidence")
#     parser.add_argument("audio", help="Path to audio file (wav/mp3/ogg/m4a)")
#     parser.add_argument("lang", help="Language code (ur/en/hi)", nargs='?', default='ur')
#     parser.add_argument("--model", help="Whisper model size (tiny|base|small|medium|large)", default="small")
#     parser.add_argument("--json", help="Output JSON instead of pipe format", action="store_true")
#     parser.add_argument("--no-roman", help="Do not attempt romanization", action="store_true")
#     args = parser.parse_args()

#     audio = args.audio
#     lang = args.lang
#     model_size = args.model

#     if not os.path.exists(audio):
#         out = {
#             "success": False,
#             "error": "file_not_found",
#             "message": f"Audio file not found: {audio}"
#         }
#         if args.json:
#             print(json.dumps(out))
#         else:
#             print(json.dumps(out))  # JSON fallback so Laravel can parse it
#         sys.exit(1)

#     try:
#         # load model (cached)
#         wmodel = load_whisper_model(size=model_size)

#         # transcribe
#         # use task=transcribe and language hint
#         result = wmodel.transcribe(audio, language=lang)

#         urdu_text = result.get("text", "").strip()
#         confidence = compute_confidence(result)

#         roman_text = ""
#         if not args.no_roman:
#             try:
#                 roman_text = romanize_text(urdu_text)
#             except Exception:
#                 roman_text = ""

#         raw = f"{urdu_text}||{roman_text}||{confidence}"

#         if args.json:
#             print(json.dumps({
#                 "success": True,
#                 "urdu_text": urdu_text,
#                 "roman_text": roman_text,
#                 "confidence": confidence,
#                 "raw": raw
#             }, ensure_ascii=False))
#         else:
#             # IMPORTANT: print EXACT required format for Laravel
#             print(f"{urdu_text}||{roman_text}||{confidence}")

#     except Exception as e:
#         tb = traceback.format_exc()
#         out = {
#             "success": False,
#             "error": "transcription_error",
#             "message": str(e),
#             "traceback": tb
#         }
#         # keep Urdu + Roman fields friendly for Laravel display
#         urdu_err = "خرابی: " + str(e)
#         roman_err = "Error: " + str(e)
#         if args.json:
#             out.update({"urdu_text": urdu_err, "roman_text": roman_err, "confidence": 0.0})
#             print(json.dumps(out, ensure_ascii=False))
#         else:
#             # Print JSON so Laravel can detect the failure easily
#             print(json.dumps({
#                 "success": False,
#                 "error": str(e),
#                 "urdu_text": urdu_err,
#                 "roman_text": roman_err,
#                 "confidence": 0.0,
#                 "traceback": tb[:4000]  # truncate large traces
#             }, ensure_ascii=False))
#         sys.exit(1)

# if __name__ == "__main__":
#     main()


import google.generativeai as genai
import sys
import os

genai.configure(api_key=os.getenv("GEMINI_API_KEY"))

audio_path = sys.argv[1]

model = genai.GenerativeModel("gemini-1.5-pro")

with open(audio_path, "rb") as f:
    audio_bytes = f.read()

prompt = """
You are a forensic transcription assistant.
Convert the following audio into accurate text transcription.
"""

response = model.generate_content([
    prompt,
    {
        "mime_type": "audio/mpeg",
        "data": audio_bytes
    }
])

print(response.text)
