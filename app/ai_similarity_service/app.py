from flask import Flask, request, jsonify
from sentence_transformers import SentenceTransformer, util
import torch

app = Flask(__name__)

# ✅ Load multilingual model (Urdu, Roman Urdu, English)
model = SentenceTransformer('paraphrase-multilingual-MiniLM-L12-v2')

@app.route('/embed', methods=['POST'])
def embed_text():
    """
    Generate embedding for a single text.
    Request JSON: {'text': 'some text'}
    Response: {'embedding': [...]}
    """
    data = request.get_json()
    text = data.get('text', '').strip()
    text = ' '.join(text.split())  # remove multiple spaces/newlines

    if not text:
        return jsonify({'embedding': []})

    embedding = model.encode(text, convert_to_tensor=True)
    return jsonify({'embedding': embedding.cpu().tolist()})


@app.route('/similarity', methods=['POST'])
def similarity():
    """
    Compare query text embedding against candidates with embeddings.
    Safe for dimension mismatches.
    
    Request JSON:
    {
        'text': 'new complaint description',
        'candidates': [
            {'id': 1, 'text': '...', 'embedding': [...]},
            ...
        ],
        'top_k': 1,
        'threshold': 0.25
    }
    Response: {'matches': [...]}
    """
    data = request.get_json()
    query_text = data.get('text', '').strip()
    candidates = data.get('candidates', [])
    top_k = int(data.get('top_k', 1))
    threshold = float(data.get('threshold', 0.25))

    if not query_text or not candidates:
        return jsonify({'matches': []})

    # Embed query text
    query_emb = model.encode(query_text, convert_to_tensor=True)
    query_dim = query_emb.shape[0]

    # Filter candidates with same embedding size
    candidate_embeddings = []
    valid_candidates = []

    for c in candidates:
        try:
            emb = torch.tensor(c['embedding'])
            if emb.shape[0] == query_dim:
                candidate_embeddings.append(emb)
                valid_candidates.append(c)
        except Exception:
            continue  # skip invalid embeddings

    if not candidate_embeddings:
        # Safe fallback: no matches possible
        return jsonify({'matches': []})

    # Stack candidate embeddings
    corpus_emb = torch.stack(candidate_embeddings)

    # Cosine similarity
    sims = util.cos_sim(query_emb, corpus_emb)[0]  # shape: [num_candidates]

    # Build results above threshold
    results = []
    for i, score in enumerate(sims):
        sim_score = score.item()
        if sim_score >= threshold:
            results.append({
                'id': valid_candidates[i]['id'],
                'text': valid_candidates[i]['text'],
                'similarity': round(sim_score * 100, 2)
            })

    # Sort descending
    results = sorted(results, key=lambda x: x['similarity'], reverse=True)

    # Return top_k matches
    return jsonify({'matches': results[:top_k]})


if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5005, debug=True)
