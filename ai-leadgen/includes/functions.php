<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function old(array $source, string $key, string $default = ''): string
{
    return isset($source[$key]) ? (string) $source[$key] : $default;
}

function generateCampaignName(string $niche, string $country, ?string $city = null): string
{
    $suffix = $city !== null && trim($city) !== '' ? $city : $country;

    return trim($niche) . ' - ' . trim($suffix);
}

function parseJobTitles(string $jobTitlesJson): array
{
    $decoded = json_decode($jobTitlesJson, true);

    if (!is_array($decoded)) {
        return [];
    }

    $clean = [];
    foreach ($decoded as $title) {
        $title = trim((string) $title);
        if ($title !== '') {
            $clean[] = $title;
        }
    }

    return array_values(array_unique($clean));
}

function validateCampaignInput(array $input): array
{
    $errors = [];

    $niche = trim((string)($input['niche'] ?? ''));
    $country = trim((string)($input['country'] ?? ''));
    $city = trim((string)($input['city'] ?? ''));
    $companySize = trim((string)($input['company_size'] ?? ''));
    $jobTitlesRaw = (string)($input['job_titles'] ?? '[]');
    $outreachTone = trim((string)($input['outreach_tone'] ?? ''));
    $dailyLimitRaw = (string)($input['daily_limit'] ?? '');

    $allowedCompanySizes = ['1-10', '11-50', '51-200', '201-500', '501-1000', '1000+'];
    $allowedTones = ['Formal', 'Friendly', 'Aggressive'];

    if ($niche === '' || mb_strlen($niche) > 255) {
        $errors['niche'] = 'Please enter a valid target niche (max 255 characters).';
    }

    if ($country === '' || mb_strlen($country) > 100) {
        $errors['country'] = 'Please enter a valid country (max 100 characters).';
    }

    if ($city !== '' && mb_strlen($city) > 100) {
        $errors['city'] = 'City can be up to 100 characters.';
    }

    if (!in_array($companySize, $allowedCompanySizes, true)) {
        $errors['company_size'] = 'Please select a valid company size.';
    }

    $jobTitles = parseJobTitles($jobTitlesRaw);
    if (count($jobTitles) === 0) {
        $errors['job_titles'] = 'Please add at least one job title.';
    }

    if (!in_array($outreachTone, $allowedTones, true)) {
        $errors['outreach_tone'] = 'Please select a valid outreach tone.';
    }

    if ($dailyLimitRaw === '' || !ctype_digit($dailyLimitRaw)) {
        $errors['daily_limit'] = 'Daily outreach limit must be a number between 1 and 500.';
    }

    $dailyLimit = (int)$dailyLimitRaw;
    if ($dailyLimit < 1 || $dailyLimit > 500) {
        $errors['daily_limit'] = 'Daily outreach limit must be between 1 and 500.';
    }

    return [
        'errors' => $errors,
        'values' => [
            'niche' => $niche,
            'country' => $country,
            'city' => $city,
            'company_size' => $companySize,
            'job_titles' => $jobTitles,
            'outreach_tone' => $outreachTone,
            'daily_limit' => $dailyLimit,
        ],
    ];
}

function toneDescription(string $tone): string
{
    $map = [
        'Formal' => 'Corporate style outreach',
        'Friendly' => 'Casual and warm',
        'Aggressive' => 'Salesy and urgent',
    ];

    return $map[$tone] ?? '';
}
