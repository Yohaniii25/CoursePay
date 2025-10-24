function gem_get_course_counts() {
    // Path to CoursePay DB class
    $db_class_path = $_SERVER['DOCUMENT_ROOT'] . '/gem/CoursePay/classes/db.php';
    if (!file_exists($db_class_path)) {
        return 'Error: CoursePay database file not found.';
    }
    require_once($db_class_path);
    
    // Initialize DB connection using CoursePay's class
    $database = new Database();
    $conn = $database->getConnection();
    if (!$conn) {
        return 'Error: Could not connect to CoursePay database.';
    }
    
    // Query: Count distinct students per course_type where payment is complete
    $sql = "
        SELECT 
            a.course_type,
            COUNT(DISTINCT a.student_id) AS student_count
        FROM applications a
        INNER JOIN payments p ON a.id = p.application_id
        WHERE p.status = 'completed'
        GROUP BY a.course_type
    ";
    
    $result = $conn->query($sql);
    $output = '<div class="gem-course-counts-wrapper">';
    $output .= '<div class="gem-course-counts-header">';
    $output .= '<h2 class="gem-section-title">Student Enrollment Overview</h2>';
    $output .= '<p class="gem-section-subtitle">Active students with completed payments</p>';
    $output .= '</div>';
    $output .= '<div class="gem-course-counts-grid">';
    
    $total = 0;
    $courses = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
            $total += (int)$row['student_count'];
        }
        
        // Display individual courses
        foreach ($courses as $course) {
            $type = esc_html($course['course_type']);
            $count = (int)$course['student_count'];
            $output .= "
                <div class='gem-course-card'>
                    <div class='gem-course-icon'>
                        <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'>
                            <path d='M22 10v6M2 10l10-5 10 5-10 5z'/>
                            <path d='M6 12v5c3 3 9 3 12 0v-5'/>
                        </svg>
                    </div>
                    <h3 class='gem-course-title'>{$type}</h3>
                    <div class='gem-student-count'>{$count}</div>
                    <p class='gem-course-label'>Enrolled Students</p>
                </div>
            ";
        }
        
        // Add total students card
        $output .= "
            <div class='gem-course-card gem-total-card'>
                <div class='gem-course-icon'>
                    <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'>
                        <path d='M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2'/>
                        <circle cx='9' cy='7' r='4'/>
                        <path d='M23 21v-2a4 4 0 0 0-3-3.87'/>
                        <path d='M16 3.13a4 4 0 0 1 0 7.75'/>
                    </svg>
                </div>
                <h3 class='gem-course-title'>Total Enrollment</h3>
                <div class='gem-student-count'>{$total}</div>
                <p class='gem-course-label'>All Students</p>
            </div>
        ";
    } else {
        $output .= "<div class='gem-no-data'><p>No completed student records found.</p></div>";
    }
    
    $output .= '</div></div>';
    $conn->close();
    return $output;
}

// Shortcode: [course_counts]
add_shortcode('course_counts', 'gem_get_course_counts');

// Enhanced styling
add_action('wp_head', function() {
    echo '<style>
        .gem-course-counts-wrapper {
            padding: 40px 20px;
            max-width: 1400px;
            margin: 0 auto;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        
        .gem-course-counts-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .gem-section-title {
            font-size: 36px;
            font-weight: 700;
            color: #1a202c;
            margin: 0 0 10px 0;
            letter-spacing: -0.5px;
        }
        
        .gem-section-subtitle {
            font-size: 18px;
            color: #718096;
            margin: 0;
            font-weight: 400;
        }
        
        .gem-course-counts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }
        
        .gem-course-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 35px 25px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            color: #fff;
        }
        
        .gem-course-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .gem-course-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.4);
        }
        
        .gem-course-card:hover::before {
            opacity: 1;
        }
        
        .gem-course-card:nth-child(2) {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            box-shadow: 0 10px 30px rgba(240, 147, 251, 0.3);
        }
        
        .gem-course-card:nth-child(3) {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            box-shadow: 0 10px 30px rgba(79, 172, 254, 0.3);
        }
        
        .gem-course-card:nth-child(4) {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            box-shadow: 0 10px 30px rgba(67, 233, 123, 0.3);
        }
        
        .gem-total-card {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%) !important;
            box-shadow: 0 10px 30px rgba(250, 112, 154, 0.3) !important;
            grid-column: span 1;
        }
        
        .gem-course-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }
        
        .gem-course-icon svg {
            width: 30px;
            height: 30px;
            stroke: #fff;
        }
        
        .gem-course-title {
            font-size: 22px;
            font-weight: 600;
            margin: 0 0 15px 0;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .gem-student-count {
            font-size: 48px;
            font-weight: 700;
            margin: 15px 0;
            color: #fff;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            line-height: 1;
        }
        
        .gem-course-label {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .gem-no-data {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            background: #f7fafc;
            border-radius: 16px;
            color: #718096;
        }
        
        @media (max-width: 768px) {
            .gem-course-counts-wrapper {
                padding: 30px 15px;
            }
            
            .gem-section-title {
                font-size: 28px;
            }
            
            .gem-section-subtitle {
                font-size: 16px;
            }
            
            .gem-course-counts-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .gem-student-count {
                font-size: 40px;
            }
        }
        
        @media (min-width: 1200px) {
            .gem-course-counts-grid {
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            }
        }
    </style>';
});