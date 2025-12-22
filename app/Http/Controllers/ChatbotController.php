<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    protected $chatbot;
    
    public function __construct()
    {
        $this->chatbot = new ChatbotService();
    }
    
    // API endpoint for chatbot
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);
        
        $response = $this->chatbot->getResponse($request->message);
        
        return response()->json([
            'success' => true,
            'response' => $response['answer'],
            'confidence' => $response['confidence'],
            'suggestions' => $this->chatbot->getSuggestions($request->message)
        ]);
    }
    
    // Show chatbot interface
    public function show()
    {
        $questions = $this->chatbot->getAllQuestions();
        return view('public_user.chatbot-index', compact('questions'));
    }
}