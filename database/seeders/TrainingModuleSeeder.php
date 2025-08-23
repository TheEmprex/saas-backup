<?php

namespace Database\Seeders;

use App\Models\TrainingModule;
use App\Models\TrainingTest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrainingModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample training modules
        $module1 = TrainingModule::create([
            'title' => 'Customer Service Fundamentals',
            'description' => 'Learn the basics of providing excellent customer service in chat support environments.',
            'content' => 'This module covers essential customer service skills including active listening, empathy, problem-solving, and professional communication. You will learn how to handle various customer scenarios and maintain a positive, helpful attitude in all interactions.',
            'duration_minutes' => 45,
            'is_active' => true,
            'order' => 1
        ]);

        $module2 = TrainingModule::create([
            'title' => 'Chat Communication Best Practices',
            'description' => 'Master the art of effective written communication in chat environments.',
            'content' => 'This module focuses on written communication skills specific to chat support. Topics include proper grammar and spelling, tone management, using emojis appropriately, managing multiple conversations, and maintaining professionalism in text-based interactions.',
            'duration_minutes' => 60,
            'is_active' => true,
            'order' => 2
        ]);

        $module3 = TrainingModule::create([
            'title' => 'Platform-Specific Guidelines',
            'description' => 'Understand the specific requirements and guidelines for different chat platforms.',
            'content' => 'Learn about the unique features, requirements, and guidelines for various chat platforms including OnlyFans, social media platforms, and dating apps. This module covers platform-specific etiquette, content guidelines, and safety protocols.',
            'duration_minutes' => 75,
            'is_active' => true,
            'order' => 3
        ]);

        // Create tests for each module
        TrainingTest::create([
            'title' => 'Customer Service Knowledge Check',
            'description' => 'Test your understanding of customer service fundamentals and best practices.',
            'questions' => [
                [
                    'question' => 'What is the most important aspect of customer service?',
                    'options' => ['Speed', 'Empathy and understanding', 'Product knowledge', 'Following scripts'],
                    'correct_answer' => 1
                ],
                [
                    'question' => 'How should you respond to an angry customer?',
                    'options' => ['Match their energy', 'Remain calm and empathetic', 'Transfer immediately', 'End the conversation'],
                    'correct_answer' => 1
                ],
                [
                    'question' => 'What should you do if you don\'t know the answer to a customer\'s question?',
                    'options' => ['Guess the answer', 'Say you don\'t know and end chat', 'Acknowledge and find the correct information', 'Change the subject'],
                    'correct_answer' => 2
                ]
            ],
            'time_limit_minutes' => 15,
            'passing_score' => 70,
            'training_module_id' => $module1->id,
            'is_active' => true
        ]);

        TrainingTest::create([
            'title' => 'Chat Communication Assessment',
            'description' => 'Evaluate your written communication skills for chat support.',
            'questions' => [
                [
                    'question' => 'Which greeting is most appropriate for a professional chat?',
                    'options' => ['Hey!', 'Hello! How can I help you today?', 'What\'s up?', 'Hi there!!! 😊😊😊'],
                    'correct_answer' => 1
                ],
                [
                    'question' => 'How should you handle typos in your messages?',
                    'options' => ['Ignore them', 'Send a correction immediately', 'Only correct if it changes meaning', 'Apologize profusely'],
                    'correct_answer' => 2
                ],
                [
                    'question' => 'What is the ideal response time for chat messages?',
                    'options' => ['Within 30 seconds', 'Within 2-3 minutes', 'Within 5 minutes', 'Whenever convenient'],
                    'correct_answer' => 1
                ]
            ],
            'time_limit_minutes' => 20,
            'passing_score' => 75,
            'training_module_id' => $module2->id,
            'is_active' => true
        ]);

        TrainingTest::create([
            'title' => 'Platform Guidelines Quiz',
            'description' => 'Test your knowledge of platform-specific guidelines and requirements.',
            'questions' => [
                [
                    'question' => 'What should you prioritize when working on different platforms?',
                    'options' => ['Personal preferences', 'Platform-specific guidelines', 'General chat rules', 'Client demands only'],
                    'correct_answer' => 1
                ],
                [
                    'question' => 'How should you handle requests that violate platform guidelines?',
                    'options' => ['Comply to keep client happy', 'Politely decline and explain', 'Ignore the request', 'Report immediately'],
                    'correct_answer' => 1
                ],
                [
                    'question' => 'What information should you never share about yourself?',
                    'options' => ['Your experience level', 'Personal contact information', 'Your interests', 'Your working hours'],
                    'correct_answer' => 1
                ]
            ],
            'time_limit_minutes' => 25,
            'passing_score' => 80,
            'training_module_id' => $module3->id,
            'is_active' => true
        ]);
    }
}
