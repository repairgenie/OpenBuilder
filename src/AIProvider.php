<?php
// src/AIProvider.php

class AIProvider {
    private $api_key;
    private $model = 'gemini-2.5-flash-preview-09-2025';

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    public function generateReport($field_notes, $weather = '', $lang = 'en') {
        if (!$this->api_key) return "AI Generation skipped. GEMINI_API_KEY not found.";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->api_key;
        $prompt = $this->getPrompt($field_notes, $weather, $lang);
        $data = ["contents" => [["parts" => [["text" => $prompt]]]]];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($http_code !== 200) return "AI Error: Received HTTP " . $http_code;
        $result = json_decode($response, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? "Error.";
    }

    public function generateProjectReport($data, $lang = 'en') {
        if (!$this->api_key) return "AI Key missing.";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->api_key;
        $context = json_encode($data);
        $prompt = ($lang === 'es')
            ? "Genera un informe ejecutivo de finalización de proyecto en ESPAÑOL basado en estos datos: " . $context . ". Resume el presupuesto, las RFIs clave y el progreso general."
            : "Generate an executive project completion report in ENGLISH based on this data: " . $context . ". Summarize budget performance, key RFIs, and overall progress.";
        $body = ["contents" => [["parts" => [["text" => $prompt]]]]];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $res = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($res, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? "Error.";
    }

    public function generateRFIDraft($issue, $lang = 'en') {
        if (!$this->api_key) return "AI Key missing.";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->api_key;
        $prompt = ($lang === 'es')
            ? "Eres un ingeniero civil. Redacta una Solicitud de Información (RFI) formal en ESPAÑOL basada en este problema: " . $issue . ". Incluye Asunto, Descripción y una pregunta clara."
            : "You are a civil engineer. Draft a formal Request for Information (RFI) in ENGLISH based on this issue: " . $issue . ". Include Subject, Description, and a clear Question.";
        $body = ["contents" => [["parts" => [["text" => $prompt]]]]];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $res = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($res, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? "Error.";
    }

    public function generateHandoverDoc($project_data, $lang = 'en') {
        if (!$this->api_key) return "AI Key missing.";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->api_key;
        $prompt = ($lang === 'es')
            ? "Genera un documento formal de entrega de proyecto (Handover) en ESPAÑOL. Resume las lecciones aprendidas, los contactos clave y el estado final del presupuesto."
            : "Generate a formal project handover document in ENGLISH. Summarize lessons learned, key contacts, and final budget status.";
        $body = ["contents" => [["parts" => [["text" => $prompt]]]]];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $res = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($res, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? "Error.";
    }

    public function prioritizeTasks($tasks, $lang = 'en') {
        if (!$this->api_key) return "AI Key missing.";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->api_key;
        $prompt = ($lang === 'es')
            ? "Prioriza estas tareas de construcción en ESPAÑOL basado en el impacto al cronograma. Justifica cada prioridad brevemente."
            : "Prioritize these construction tasks in ENGLISH based on schedule impact. Justify each priority briefly.";
        $body = ["contents" => [["parts" => [["text" => $prompt]]]]];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $res = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($res, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? "Error.";
    }

    public function getBudgetTips($budget_data, $lang = 'en') {
        if (!$this->api_key) return "AI Key missing.";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->api_key;
        $prompt = ($lang === 'es')
            ? "Analiza estos datos de presupuesto en ESPAÑOL: " . json_encode($budget_data) . ". Sugiere 3 formas de optimizar gastos."
            : "Analyze this budget data in ENGLISH: " . json_encode($budget_data) . ". Suggest 3 ways to optimize spending.";
        $body = ["contents" => [["parts" => [["text" => $prompt]]]]];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $res = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($res, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? "Error.";
    }

    public function generatePhotoCaption($photo_context, $lang = 'en') {
        if (!$this->api_key) return "AI Key missing.";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->api_key;
        $prompt = ($lang === 'es')
            ? "Genera una descripción técnica en ESPAÑOL para una foto de obra con este contexto: " . $photo_context . "."
            : "Generate a technical description in ENGLISH for a construction photo with this context: " . $photo_context . ".";
        $body = ["contents" => [["parts" => [["text" => $prompt]]]]];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $res = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($res, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? "Error.";
    }

    public function generateEmailSummary($data, $lang = 'en') {
        if (!$this->api_key) return "AI Key missing.";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->api_key;
        $prompt = ($lang === 'es')
            ? "Genera un correo electrónico profesional en ESPAÑOL resumiendo el progreso semanal: " . json_encode($data) . "."
            : "Generate a professional email in ENGLISH summarizing the weekly progress: " . json_encode($data) . ".";
        $body = ["contents" => [["parts" => [["text" => $prompt]]]]];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $res = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($res, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? "Error.";
    }

    public function generatePortfolioInsights($portfolio_data, $lang = 'en') {
        if (!$this->api_key) return "AI Key missing.";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->api_key;
        $context = json_encode($portfolio_data);
        $prompt = ($lang === 'es')
            ? "Analiza el rendimiento de esta cartera de construcción en ESPAÑOL: " . $context . ". Identifica los 3 proyectos con mayor riesgo y sugiere estrategias de mitigación."
            : "Analyze the performance of this construction portfolio in ENGLISH: " . $context . ". Identify the 3 highest-risk projects and suggest mitigation strategies.";
        
        $body = ["contents" => [["parts" => [["text" => $prompt]]]]];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $res = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($res, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? "Error.";
    }

    public function detectFinancialAnomalies($transactions, $lang = 'en') {
        if (!$this->api_key) return "AI Key missing.";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->api_key;
        $context = json_encode($transactions);
        $prompt = ($lang === 'es')
            ? "Analiza estas transacciones financieras en ESPAÑOL: " . $context . ". Identifica posibles anomalías, errores de facturación o riesgos de fraude."
            : "Analyze these financial transactions in ENGLISH: " . $context . ". Identify potential anomalies, billing errors, or fraud risks.";
        
        $body = ["contents" => [["parts" => [["text" => $prompt]]]]];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $res = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($res, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? "Error.";
    }

    public function generateSubmittalLog($spec_text, $lang = 'en') {
        if (!$this->api_key) return "AI Key missing.";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->api_key;
        $prompt = ($lang === 'es')
            ? "Analiza este texto de especificación en ESPAÑOL y genera un registro de submittals requeridos. Para cada uno, identifica la sección y el tipo (ej. Planos de taller, Muestra)."
            : "Analyze this spec text in ENGLISH and generate a required submittals log. For each, identify the section and type (e.g. Shop Drawings, Sample).";
        
        $body = ["contents" => [["parts" => [["text" => $prompt]]]]];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $res = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($res, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? "Error.";
    }

    public function classifyPunchItem($description, $lang = 'en') {
        if (!$this->api_key) return "AI Key missing.";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->api_key;
        $prompt = ($lang === 'es')
            ? "Clasifica esta deficiencia de construcción en ESPAÑOL: '" . $description . "'. Identifica la disciplina (ej. Pintura, Eléctrico) y asigna una prioridad."
            : "Classify this construction deficiency in ENGLISH: '" . $description . "'. Identify the discipline (e.g. Painting, Electrical) and assign a priority.";
        
        $body = ["contents" => [["parts" => [["text" => $prompt]]]]];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $res = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($res, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? "Error.";
    }

    public function predictOverruns($financial_data, $lang = 'en') {
        if (!$this->api_key) return "AI Key missing.";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->api_key;
        $context = json_encode($financial_data);
        $prompt = ($lang === 'es')
            ? "Predice el riesgo de sobrecostos en ESPAÑOL basado en estos datos financieros: " . $context . ". Identifica los códigos de costo más vulnerables."
            : "Predict cost overrun risk in ENGLISH based on this financial data: " . $context . ". Identify the most vulnerable cost codes.";
        
        $body = ["contents" => [["parts" => [["text" => $prompt]]]]];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $res = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($res, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? "Error.";
    }

    public function analyzeBids($bids_data, $lang = 'en') {
        if (!$this->api_key) return "AI Key missing.";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->api_key;
        $context = json_encode($bids_data);
        $prompt = ($lang === 'es')
            ? "Analiza estas ofertas de construcción en ESPAÑOL: " . $context . ". Identifica anomalías en los precios (demasiado altos o bajos) y sugiere al mejor proveedor basado en costo y valor."
            : "Analyze these construction bids in ENGLISH: " . $context . ". Identify pricing anomalies (too high or too low) and suggest the best vendor based on cost and value.";
        $body = ["contents" => [["parts" => [["text" => $prompt]]]]];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $res = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($res, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? "Error.";
    }

    private function getPrompt($notes, $weather, $lang) {
        $weather_context = $weather ? " The weather today was: " . $weather . "." : "";
        if ($lang === 'es') {
            return "Eres un experto gerente de proyectos de construcción." . $weather_context . " Transforma las siguientes notas de campo en un informe diario profesional en ESPAÑOL. Limpia las abreviaturas, escribe en oraciones completas. Detecta y enumera explícitamente cualquier riesgo de seguridad o de cronograma mencionado.\n\nNotas de campo:\n" . $notes . "\n\nFormatea la respuesta con Markdown usando encabezados claros para 'Trabajo Realizado', 'Riesgos de Seguridad' y 'Riesgos de Cronograma'.";
        }
        return "You are an expert construction project manager." . $weather_context . " Transform the following field notes into a professional daily report in ENGLISH. Clean up the shorthand, write in complete sentences. Detect and explicitly list any safety risks or scheduling risks mentioned.\n\nField Notes:\n" . $notes . "\n\nFormat the response with Markdown using clear headings for 'Work Performed', 'Safety Risks', and 'Scheduling Risks'.";
    }
}
