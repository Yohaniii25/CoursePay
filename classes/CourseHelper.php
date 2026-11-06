<?php

class CourseHelper
{
    public static function getPartTimeCourses(): array
    {
        return [
            'Certificate in Basic Gemmology',
            'Certificate in Gemmology',
            'Certificate in Blended Gemmology',
            'Certificate in Geuda Heat Treatment',
            'Certificate in Computer Aided Jewellery Designing and Manufacturing (CAD/CAM)',
            'Certificate in Jewellery Stone Setting (NVQ 3)',
            'Certificate in Jewellery Casting and Electro Plating',
            'Certificate in Jewellery Assaying and Hallmarking',
            'Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)/ Diploma in Professional Jewellery (Dip. PJSL)',
            'Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)'
        ];
    }

    public static function getFullTimeCourses(): array
    {
        return [
            'Certificate in Gem Cutting and Polishing (NVQ 3)',
            'Certificate in Jewellery Designing (Manual)',
            'Certificate in Jewellery Designing Technology (NVQ 4)',
            'Certificate in Jewellery Manufacturing (NVQ 3)',
            'Certificate in Jewellery Manufacturing (NVQ 4)',
            'Certificate in Jewellery Manufacturing'
        ];
    }

    /**
     * Resolves course mode ('Full-Time', 'Part-Time', or '')
     */
    public static function resolveCourseMode(?string $courseName): string
    {
        if (empty($courseName)) {
            return '';
        }

        $trimmed = trim($courseName);
        $partTime = self::getPartTimeCourses();
        $fullTime = self::getFullTimeCourses();

        // Exact match
        if (in_array($trimmed, $partTime, true)) {
            return 'Part-Time';
        }
        if (in_array($trimmed, $fullTime, true)) {
            return 'Full-Time';
        }

        // Case-insensitive match
        $lower = strtolower($trimmed);
        foreach ($partTime as $pt) {
            if (strtolower($pt) === $lower) {
                return 'Part-Time';
            }
        }
        foreach ($fullTime as $ft) {
            if (strtolower($ft) === $lower) {
                return 'Full-Time';
            }
        }

        return '';
    }
}

