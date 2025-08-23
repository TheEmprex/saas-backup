<?php

namespace Database\Seeders;

use App\Models\TypingTest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypingTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // English typing tests
        TypingTest::create([
            'title' => 'Basic English Typing Test',
            'language' => 'en',
            'content' => 'The quick brown fox jumps over the lazy dog. This sentence contains every letter of the alphabet and is commonly used for typing practice. Good typing skills are essential for many jobs, especially in customer service and content creation roles.',
            'difficulty_level' => 1,
            'time_limit_seconds' => 300,
            'min_wpm' => 30,
            'min_accuracy' => 85.00,
            'is_active' => true,
        ]);

        TypingTest::create([
            'title' => 'Professional English Typing Test',
            'language' => 'en',
            'content' => 'In today\'s digital world, effective communication through written text is crucial. Whether you are responding to customer inquiries, creating content, or managing online conversations, your typing speed and accuracy directly impact your productivity and professional image.',
            'difficulty_level' => 2,
            'time_limit_seconds' => 240,
            'min_wpm' => 40,
            'min_accuracy' => 90.00,
            'is_active' => true,
        ]);

        TypingTest::create([
            'title' => 'Advanced English Typing Test',
            'language' => 'en',
            'content' => 'Customer service representatives must demonstrate exceptional communication skills, including rapid and accurate typing abilities. They handle multiple conversations simultaneously while maintaining professionalism and attention to detail throughout their interactions.',
            'difficulty_level' => 3,
            'time_limit_seconds' => 180,
            'min_wpm' => 50,
            'min_accuracy' => 92.00,
            'is_active' => true,
        ]);

        // French typing tests
        TypingTest::create([
            'title' => 'Test de Frappe Français de Base',
            'language' => 'fr',
            'content' => 'Bonjour et bienvenue dans ce test de frappe en français. La maîtrise de la frappe rapide et précise est essentielle pour de nombreux emplois, particulièrement dans le service clientèle et la création de contenu en ligne.',
            'difficulty_level' => 1,
            'time_limit_seconds' => 300,
            'min_wpm' => 25,
            'min_accuracy' => 85.00,
            'is_active' => true,
        ]);

        TypingTest::create([
            'title' => 'Test de Frappe Français Professionnel',
            'language' => 'fr',
            'content' => 'Dans le monde numérique d\'aujourd\'hui, la communication efficace par écrit est cruciale. Que vous répondiez aux demandes des clients, créiez du contenu ou gériez des conversations en ligne, votre vitesse et précision de frappe impactent directement votre productivité.',
            'difficulty_level' => 2,
            'time_limit_seconds' => 240,
            'min_wpm' => 35,
            'min_accuracy' => 90.00,
            'is_active' => true,
        ]);

        TypingTest::create([
            'title' => 'Test de Frappe Français Avancé',
            'language' => 'fr',
            'content' => 'Les représentants du service clientèle doivent démontrer des compétences de communication exceptionnelles, incluant des capacités de frappe rapide et précise. Ils gèrent plusieurs conversations simultanément tout en maintenant le professionnalisme et l\'attention aux détails.',
            'difficulty_level' => 3,
            'time_limit_seconds' => 180,
            'min_wpm' => 45,
            'min_accuracy' => 92.00,
            'is_active' => true,
        ]);
    }
}
