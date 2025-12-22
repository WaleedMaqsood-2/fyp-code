<!-- resources/views/chatbot/index.blade.php -->
<div class="chatbot-container">
    <div class="chatbot-header">
        <h4>پولیس ہیلپ بوٹ</h4>
        <p>آپ کے سوالات کے جوابات</p>
    </div>
    
    <div class="chatbot-body" id="chatMessages">
        <!-- Messages will appear here -->
        <div class="bot-message">
            سلام! میں پولیس پورٹل کا ہیلپ بوٹ ہوں۔ آپ مجھ سے درج ذیل سوالات پوچھ سکتے ہیں:
            <ul>
                @foreach($questions as $question)
                    <li>{{ $question }}؟</li>
                @endforeach
            </ul>
        </div>
    </div>
    
    <div class="chatbot-footer">
        <input type="text" id="userInput" placeholder="اپنا سوال یہاں لکھیں..." class="form-control">
        <button onclick="sendMessage()" class="btn btn-primary">بھیجیں</button>
    </div>
</div>

<script>
let chatHistory = [];

function sendMessage() {
    const input = document.getElementById('userInput');
    const message = input.value.trim();
    
    if (!message) return;
    
    // Add user message
    addMessage(message, 'user');
    
    // Clear input
    input.value = '';
    
    // Send to server
    fetch('/api/chatbot/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addMessage(data.response, 'bot');
            
            // Add suggestions if available
            if (data.suggestions && data.suggestions.length > 0) {
                addSuggestions(data.suggestions);
            }
        }
    });
}

function addMessage(text, sender) {
    const container = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `${sender}-message`;
    messageDiv.innerHTML = text;
    container.appendChild(messageDiv);
    
    // Scroll to bottom
    container.scrollTop = container.scrollHeight;
}

function addSuggestions(suggestions) {
    const container = document.getElementById('chatMessages');
    const suggestionsDiv = document.createElement('div');
    suggestionsDiv.className = 'suggestions';
    suggestionsDiv.innerHTML = '<strong>مزید سوالات:</strong><br>' + 
        suggestions.map(q => `<button onclick="askQuestion('${q}')">${q}؟</button>`).join(' ');
    container.appendChild(suggestionsDiv);
}

function askQuestion(question) {
    document.getElementById('userInput').value = question;
    sendMessage();
}

// Enter key support
document.getElementById('userInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});
</script>

<style>
.chatbot-container {
    border: 1px solid #ddd;
    border-radius: 10px;
    max-width: 500px;
    margin: auto;
}
.chatbot-header {
    background: #007bff;
    color: white;
    padding: 15px;
    border-radius: 10px 10px 0 0;
}
.chatbot-body {
    height: 400px;
    overflow-y: auto;
    padding: 15px;
}
.chatbot-footer {
    padding: 15px;
    border-top: 1px solid #ddd;
}
.user-message {
    background: #e3f2fd;
    padding: 10px;
    margin: 10px 0;
    border-radius: 10px;
    text-align: right;
}
.bot-message {
    background: #f1f1f1;
    padding: 10px;
    margin: 10px 0;
    border-radius: 10px;
}
.suggestions button {
    margin: 2px;
    padding: 5px 10px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
</style>