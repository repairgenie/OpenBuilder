<?php
// src/AIProvider.php

class AIProvider {
    private $api_key;
    private $model = 'gemini-2.5-flash-preview-09-2025';
    private $base_url = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    private function callGemini($prompt, $lang = 'en') {
        if (!$this->api_key) return $lang === 'es'
            ? "AI no disponible. Clave API no configurada."
            : "AI unavailable. API key not configured.";

        $url = "{$this->base_url}/{$this->model}:generateContent";
        $body = json_encode(["contents" => [["parts" => [["text" => $prompt]]]]]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
'Authorization: Bearer ' . $this->api_key
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            error_log("AIProvider curl error: $curl_error");
            return $lang === 'es'
                ? "Error de comunicacion con el servicio de IA."
                : "AI service communication error.";
        }

        if ($http_code !== 200) {
            error_log("AIProvider HTTP $http_code: $response");
            return $lang === 'es'
                ? "Error del servicio de IA (HTTP $http_code)."
                : "AI service error (HTTP $http_code).";
        }

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("AIProvider JSON parse error: " . json_last_error_msg());
            return $lang === 'es' ? "Error procesando respuesta de IA." : "AI response parse error.";
        }

        return $result['candidates'][0]['content']['parts'][0]['text'] ?? "Error.";
    }

    public function generateReport($field_notes, $weather = '', $lang = 'en') {
        $weather_context = $weather ? " The weather today was: $weather." : "";
        if ($lang === 'es') {
            $prompt = "Eres un experto gerente de proyectos de construccion."
                . $weather_context
                . " Transforma las siguientes notas de campo en un informe diario profesional en ESPAÑOL."
                . " Limpia las abreviaturas, escribe en oraciones completas."
                . " Detecta y enumera explicitamente cualquier riesgo de seguridad o de cronograma mencionado."
                . "\n\nNotas de campo:\n$field_notes\n\nFormatea la respuesta con Markdown usando encabezados claros para 'Trabajo Realizado', 'Riesgos de Seguridad' y 'Riesgos de Cronograma'.";
        } else {
            $prompt = "You are an expert construction project manager."
                . $weather_context
                . " Transform the following field notes into a professional daily report in ENGLISH."
                . " Clean up the shorthand, write in complete sentences."
                . " Detect and explicitly list any safety risks or scheduling risks mentioned."
                . "\n\nField Notes:\n$field_notes\n\nFormat the response with Markdown using clear headings for 'Work Performed', 'Safety Risks', and 'Scheduling Risks'.";
        }
        return $this->callGemini($prompt, $lang);
    }

    public function generateProjectReport($data, $lang = 'en') {
        $context = json_encode($data);
        $prompt = $lang === 'es'
            ? "Genera un informe ejecutivo de finalizacion de proyecto en ESPAÑOL basado en estos datos: $context. Resume el presupuesto, las RFIs clave y el progreso general."
            : "Generate an executive project completion report in ENGLISH based on this data: $context. Summarize budget performance, key RFIs, and overall progress.";
        return $this->callGemini($prompt, $lang);
    }

    public function generateRFIDraft($issue, $lang = 'en') {
        $prompt = $lang === 'es'
            ? "Eres un ingeniero civil. Redacta una Solicitud de Informacion (RFI) formal en ESPAÑOL basada en este problema: $issue. Incluye Asunto, Descripcion y una pregunta clara."
            : "You are a civil engineer. Draft a formal Request for Information (RFI) in ENGLISH based on this issue: $issue. Include Subject, Description, and a clear Question.";
        return $this->callGemini($prompt, $lang);
    }

    public function generateHandoverDoc($project_data, $lang = 'en') {
        $prompt = $lang === 'es'
            ? "Genera un documento formal de entrega de proyecto (Handover) en ESPAÑOL. Resume las lecciones aprendidas, los contactos clave y el estado final del presupuesto."
            : "Generate a formal project handover document in ENGLISH. Summarize lessons learned, key contacts, and final budget status.";
        return $this->callGemini($prompt, $lang);
    }

    public function prioritizeTasks($tasks, $lang = 'en') {
        $prompt = $lang === 'es'
            ? "Prioriza estas tareas de construccion en ESPAÑOL basado en el impacto al cronograma. Justifica cada prioridad brevemente."
            : "Prioritize these construction tasks in ENGLISH based on schedule impact. Justify each priority briefly.";
        return $this->callGemini($prompt, $lang);
    }

    public function getBudgetTips($budget_data, $lang = 'en') {
        $prompt = $lang === 'es'
            ? "Analiza estos datos de presupuesto en ESPAÑOL: " . json_encode($budget_data) . ". Sugiere 3 formas de optimizar gastos."
            : "Analyze this budget data in ENGLISH: " . json_encode($budget_data) . ". Suggest 3 ways to optimize spending.";
        return $this->callGemini($prompt, $lang);
    }

    public function generatePhotoCaption($photo_context, $lang = 'en') {
        $prompt = $lang === 'es'
            ? "Genera una descripcion tecnica en ESPAÑOL para una foto de obra con este contexto: $photo_context."
            : "Generate a technical description in ENGLISH for a construction photo with this context: $photo_context.";
        return $this->callGemini($prompt, $lang);
    }

    public function generateEmailSummary($data, $lang = 'en') {
        $prompt = $lang === 'es'
            ? "Genera un correo electronico profesional en ESPAÑOL resumiendo el progreso semanal: " . json_encode($data) . "."
            : "Generate a professional email in ENGLISH summarizing the weekly progress: " . json_encode($data) . ".";
        return $this->callGemini($prompt, $lang);
    }

    public function generatePortfolioInsights($portfolio_data, $lang = 'en') {
        $context = json_encode($portfolio_data);
        $prompt = $lang === 'es'
            ? "Analiza el rendimiento de esta cartera de construccion en ESPAÑOL: $context. Identifica los 3 proyectos con mayor riesgo y sugiere estrategias de mitigacion."
            : "Analyze the performance of this construction portfolio in ENGLISH: $context. Identify the 3 highest-risk projects and suggest mitigation strategies.";
        return $this->callGemini($prompt, $lang);
    }

    public function detectFinancialAnomalies($transactions, $lang = 'en') {
        $context = json_encode($transactions);
        $prompt = $lang === 'es'
            ? "Analiza estas transacciones financieras en ESPAÑOL: $context. Identifica posibles anomalías, errores de facturacion o riesgos de fraude."
            : "Analyze these financial transactions in ENGLISH: $context. Identify potential anomalies, billing errors, or fraud risks.";
        return $this->callGemini($prompt, $lang);
    }

    public function generateSubmittalLog($spec_text, $lang = 'en') {
        $prompt = $lang === 'es'
            ? "Analiza este texto de especificacion en ESPAÑOL y genera un registro de submittals requeridos. Para cada uno, identifica la seccion y el tipo (ej. Planos de taller, Muestra)."
            : "Analyze this spec text in ENGLISH and generate a required submittals log. For each, identify the section and type (e.g. Shop Drawings, Sample).";
        return $this->callGemini($prompt, $lang);
    }

    public function classifyPunchItem($description, $lang = 'en') {
        $prompt = $lang === 'es'
            ? "Clasifica esta deficiencia de construccion en ESPAÑOL: '$description'. Identifica la disciplina (ej. Pintura, Electrico) y asigna una prioridad."
            : "Classify this construction deficiency in ENGLISH: '$description'. Identify the discipline (e.g. Painting, Electrical) and assign a priority.";
        return $this->callGemini($prompt, $lang);
    }

    public function predictOverruns($financial_data, $lang = 'en') {
        $context = json_encode($financial_data);
        $prompt = $lang === 'es'
            ? "Predice el riesgo de sobrecostos en ESPAÑOL basado en estos datos financieros: $context. Identifica los codigos de costo mas vulnerables."
            : "Predict cost overrun risk in ENGLISH based on this financial data: $context. Identify the most vulnerable cost codes.";
        return $this->callGemini($prompt, $lang);
    }

    public function analyzeBids($bids_data, $lang = 'en') {
        $context = json_encode($bids_data);
        $prompt = $lang === 'es'
            ? "Analiza estas ofertas de construccion en ESPAÑOL: $context. Identifica anomalías en los precios (demasiado altos o bajos) y sugiere al mejor proveedor basado en costo y valor."
            : "Analyze these construction bids in ENGLISH: $context. Identify pricing anomalies (too high or too low) and suggest the best vendor based on cost and value.";
        return $this->callGemini($prompt, $lang);
    }
}