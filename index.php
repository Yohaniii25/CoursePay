<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Application Form - GJRTI</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<?php
if (isset($_SESSION['payment_success'])) {
    $s = $_SESSION['payment_success'];
    unset($_SESSION['payment_success']); // Show only once
?>
<div class="success-banner">
    <h2>Payment Completed Successfully!</h2>
    <p><strong>Student:</strong> <?php echo htmlspecialchars($s['name']); ?></p>
    <p><strong>Reference No:</strong> <?php echo htmlspecialchars($s['ref']); ?></p>
    <p><strong>Course:</strong> <?php echo htmlspecialchars($s['course']); ?></p>
    <p><strong>Total Paid:</strong> Rs. <?php echo number_format($s['paid'], 2); ?></p>
    <p><strong>Remaining:</strong> Rs. <?php echo number_format($s['due'], 2); ?></p>
    <?php if ($s['due'] <= 0): ?>
        <p style="color: #16a34a; font-weight: 600; margin-top: 0.5rem;">Full payment received. You're all set!</p>
    <?php endif; ?>
    <button onclick="window.print()" class="print-btn">
        Print Receipt
    </button>
</div>
<?php } ?>

<header class="header">
    <div class="header-logo-container">
        <img src="https://www.gjrti.gov.lk/wp-content/uploads/2025/06/GJRT-1.png"
            alt="Gem and Jewellery Research and Training Institute Logo"
            class="header-logo">
    </div>

    <h1 class="header-title">
        Gem and Jewellery Research and Training Institute
    </h1>

    <a href="https://www.gjrti.gov.lk/" class="back-btn">
        ← Back to Website
    </a>
</header>

    <div class="form-container">
        <h1 class="form-title">Student Application Form</h1>

        <form action="submit.php" method="POST" enctype="multipart/form-data" class="app-form">

            <div class="form-group">
                <label for="name" class="form-label">
                    Full Name <span class="required-star">*</span>
                </label>
                <input type="text" id="name" name="name" required class="form-input">
            </div>

            <div class="form-group">
                <label for="contact-number" class="form-label">
                    Contact Number <span class="required-star">*</span>
                </label>
                <input type="text" id="contact-number" name="contact_number" required class="form-input">
            </div>

            <div class="form-group">
                <label for="gmail" class="form-label">
                    Email
                </label>
                <input type="email" id="gmail" name="gmail" class="form-input">
            </div>

            <div class="form-group">
                <label for="address" class="form-label">
                    Address
                </label>
                <textarea id="address" name="address" rows="3" class="form-textarea"></textarea>
            </div>

            <div class="form-group">
                <label for="regional-centre" class="form-label">
                    Regional Centre <span class="required-star">*</span>
                </label>
                <select id="regional-centre" name="regional_centre" required class="form-select">
                    <option value="">Select a centre</option>
                    <option value="Head Office - Kaduwela">Head Office - Kaduwela</option>
                    <option value="Ratnapura">Ratnapura</option>
                    <option value="Galle">Galle</option>
                    <option value="Kandy">Kandy</option>
                    <option value="Badulla">Badulla</option>
                    <option value="Nivithigala">Nivithigala</option>
                    <option value="Naula">Naula</option>
                    <option value="Attanagalla">Attanagalla</option>
                    <option value="Ratnapura (NYSC)">Ratnapura (NYSC)</option>
                    <option value="Gampola">Gampola</option>
                    <option value="Laggala">Laggala</option>
                    <option value="Maradana">Maradana</option>
                    <option value="Senapura">Senapura</option>
                    <option value="Batticaloa">Batticaloa</option>
                    <option value="Jaffna">Jaffna</option>
                </select>
            </div>

            <div class="form-group">
                <label for="course-type" class="form-label">
                    Course Type <span class="required-star">*</span>
                </label>
                <select id="course-type" name="course_type" required class="form-select">
                    <option value="">Select a type</option>
                    <option value="Certificate Level Courses">Certificate Level Courses</option>
                    <option value="Diploma Level Courses">Diploma Level Courses</option>
                    <option value="International Courses">International Courses</option>
                </select>
            </div>

            <div class="form-group">
                <label for="course" class="form-label">
                    Course <span class="required-star">*</span>
                </label>
                <select id="course" name="course" required class="form-select">
                    <option value="">Select a course</option>
                </select>
            </div>

            <div class="form-group">
                <label for="course-price" class="form-label">
                    Course Fee
                </label>
                <input type="text" id="course-price" name="course_fee_display" readonly class="form-input">
                <input type="hidden" id="reg-fee" name="reg_fee">
                <input type="hidden" id="course-fee" name="course_fee">
                <input type="hidden" id="refundable-deposit" name="refundable_deposit" value="0">
            </div>

            <div class="form-group">
                <label for="total-fee" class="form-label">
                    Total Fee
                </label>
                <input type="text" id="total-fee" name="total_fee_display" readonly class="form-input">
            </div>

            <div class="form-group">
                <label for="nic-passport" class="form-label">
                    NIC No. / Passport No. <span class="required-star">*</span>
                </label>
                <input type="text" id="nic-passport" name="nic_passport" required class="form-input">
            </div>

            <div class="form-group">
                <label for="nic-file" class="form-label">
                    Attach a copy of NIC / Passport <span class="required-star">*</span>
                </label>
                <input type="file" id="nic-file" name="nic_file" accept=".pdf,.jpg,.jpeg,.png" required class="form-input form-file">
            </div>

            <div class="form-group">
                <label for="education-background" class="form-label">
                    Educational Background
                </label>
                <textarea id="education-background" name="education_background" rows="3" class="form-textarea"></textarea>
            </div>

            <div class="declaration-box">
                <label class="declaration-label">
                    <input type="checkbox" name="declaration" value="1" required class="declaration-checkbox">
                    <span class="declaration-text">
                        I hereby declare that the above information is true and correct.
                    </span>
                </label>
            </div>
            <div class="declaration-box">
                <label class="declaration-label">
                    <input type="checkbox" name="declaration2" value="1" required class="declaration-checkbox">
                    <span class="declaration-text">
                        I have read and understand the code of bylaws published by the institution.
                    </span>
                </label>
            </div>

            <button type="submit" class="submit-btn">
                Submit Application
            </button>
        </form>
    </div>

    <footer class="footer">
        © 2025 Gem and Jewellery Research and Training Institute. All rights reserved.
    </footer>
    <script>
        const courseData = {
            "Head Office - Kaduwela": {
                "Certificate Level Courses": [
                    "Certificate in Basic Gemmology",
                    "Certificate in Gemmology",
                    "Certificate in Blended Gemmology",
                    "Certificate in Gems and Jewellery Valuation and Marketing",
                    "Certificate in Geuda Heat Treatment",
                    "Certificate in Gem Cutting and Polishing (NVQ 3)",
                    "Certificate in Gem Cutting and Polishing (NVQ 4)",
                    "Certificate in Gem Cutting and Polishing (Weekend)",
                    "Certificate in Gem Cutting and Polishing (10 DAYS)",
                    "Gem Related Certificate in Tailor – Made Courses",
                    "Certificate in Jewellery Designing (Manual)",
                    "Certificate in Jewellery Designing Technology (NVQ 4)",
                    "Certificate in Computer Aided Jewellery Designing and Manufacturing (CAD/CAM)",
                    "Certificate in Jewellery Manufacturing (NVQ 3)",
                    "Certificate in Jewellery Manufacturing (NVQ 4)",
                    "Certificate in Jewellery Stone Setting (NVQ 3)",
                    "Certificate in Jewellery Casting and Electro Plating",
                    "Certificate in Jewellery Assaying and Hallmarking",
                    "Jewellery Certificate in Tailor – Made Courses"
                ],
                "Diploma Level Courses": [
                    "Diploma in Professional Gemmology (Dip. PGSL)",
                    "Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)/ Diploma in Professional Jewellery (Dip. PJSL)"
                ],
                "International Courses": [
                    "Gem-A Foundation Course",
                    "Gem-A Diploma Course"
                ]
            },
            "Ratnapura": {
                "Certificate Level Courses": [
                    "Certificate in Basic Gemmology",
                    "Certificate in Gemmology",
                    "Certificate in Blended Gemmology",
                    "Certificate in Gems and Jewellery Valuation and Marketing",
                    "Certificate in Geuda Heat Treatment",
                    "Certificate in Gem Cutting and Polishing (NVQ 3)",
                    "Certificate in Gem Cutting and Polishing (NVQ 4)",
                    "Certificate in Gem Cutting and Polishing (Weekend)",
                    "Certificate in Gem Cutting and Polishing (10 DAYS)",
                    "Gem Related Certificate in Tailor – Made Courses",
                    "Certificate in Jewellery Designing (Manual)",
                    "Certificate in Jewellery Designing Technology (NVQ 4)",
                    "Certificate in Computer Aided Jewellery Designing and Manufacturing (CAD/CAM)",
                    "Certificate in Jewellery Manufacturing (NVQ 3)",
                    "Certificate in Jewellery Manufacturing (NVQ 4)",
                    "Certificate in Costume Jewellery Manufacturing",
                    "Certificate in Jewellery Stone Setting (NVQ 3)",
                    "Jewellery Certificate in Tailor – Made Courses"
                ],
                "Diploma Level Courses": [
                    "Diploma in Professional Gemmology (Dip. PGSL)",
                    "Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)"
                ],
                "International Courses": []
            },
            "Kandy": {
                "Certificate Level Courses": [
                    "Certificate in Basic Gemmology",
                    "Certificate in Gem Cutting and Polishing (NVQ 3)",
                    "Certificate in Gem Cutting and Polishing (NVQ 4)",
                    "Certificate in Gem Cutting and Polishing (Weekend)",
                    "Certificate in Gem Cutting and Polishing (10 DAYS)",
                    "Certificate in Jewellery Designing (Manual)",
                    "Certificate in Jewellery Manufacturing (NVQ 3)",
                    "Certificate in Jewellery Manufacturing (NVQ 4)",
                    "Certificate in Jewellery Stone Setting (NVQ 3)"
                ],
                "Diploma Level Courses": [],
                "International Courses": []
            },
            "Badulla": {
                "Certificate Level Courses": [
                    "Certificate in Basic Gemmology",
                    "Certificate in Gem Cutting and Polishing (NVQ 3)",
                    "Certificate in Gem Cutting and Polishing (NVQ 4)",
                    "Certificate in Gem Cutting and Polishing (10 DAYS)"
                ],
                "Diploma Level Courses": [],
                "International Courses": []
            },
            "Galle": {
                "Certificate Level Courses": [
                    "Certificate in Jewellery Designing (Manual)",
                    "Certificate in Jewellery Manufacturing (NVQ 3)",
                    "Certificate in Jewellery Manufacturing (NVQ 4)",
                    "Certificate in Jewellery Stone Setting (NVQ 3)"
                ],
                "Diploma Level Courses": [],
                "International Courses": []
            },
            "Nivithigala": {
                "Certificate Level Courses": [
                    "Certificate in Geuda Heat Treatment",
                    "Certificate in Gem Cutting and Polishing (NVQ 3)",
                    "Certificate in Gem Cutting and Polishing (NVQ 4)",
                    "Certificate in Gem Cutting and Polishing (Weekend)",
                    "Certificate in Gem Cutting and Polishing (10 DAYS)"
                ],
                "Diploma Level Courses": [],
                "International Courses": []
            },
            "Naula": {
                "Certificate Level Courses": [
                    "Certificate in Gem Cutting and Polishing (NVQ 3)",
                    "Certificate in Gem Cutting and Polishing (NVQ 4)",
                    "Certificate in Gem Cutting and Polishing (Weekend)",
                    "Certificate in Gem Cutting and Polishing (10 DAYS)"
                ],
                "Diploma Level Courses": [],
                "International Courses": []
            },
            "Attanagalla": {
                "Certificate Level Courses": [
                    "Certificate in Jewellery Manufacturing (NVQ 3)",
                    "Certificate in Jewellery Manufacturing (NVQ 4)",
                    "Certificate in Jewellery Stone Setting (NVQ 3)"
                ],
                "Diploma Level Courses": [],
                "International Courses": []
            },
            "Ratnapura (NYSC)": {
                "Certificate Level Courses": [
                    "Certificate in Gem Cutting and Polishing (NVQ 3)",
                    "Certificate in Gem Cutting and Polishing (NVQ 4)",
                    "Certificate in Gem Cutting and Polishing (Weekend)",
                    "Certificate in Gem Cutting and Polishing (10 DAYS)"
                ],
                "Diploma Level Courses": [],
                "International Courses": []
            },
            "Gampola": {
                "Certificate Level Courses": [
                    "Certificate in Jewellery Manufacturing (NVQ 3)",
                    "Certificate in Jewellery Manufacturing (NVQ 4)",
                ],
                "Diploma Level Courses": [],
                "International Courses": []
            },
            "Laggala": {
                "Certificate Level Courses": [
                    "Certificate in Geuda Heat Treatment",
                    "Certificate in Gem Cutting and Polishing (NVQ 3)",
                    "Certificate in Gem Cutting and Polishing (NVQ 4)",
                    "Certificate in Gem Cutting and Polishing (10 DAYS)"
                ],
                "Diploma Level Courses": [],
                "International Courses": []
            },
            "Maradana": {
                "Certificate Level Courses": [
                    "Certificate in Gem Cutting and Polishing (NVQ 3)",
                    "Certificate in Gem Cutting and Polishing (NVQ 4)",
                ],
                "Diploma Level Courses": [],
                "International Courses": []
            },
            "Senapura": {
                "Certificate Level Courses": [
                    "Certificate in Gem Cutting and Polishing (NVQ 3)",
                    "Certificate in Gem Cutting and Polishing (NVQ 4)",
                    "Certificate in Jewellery Manufacturing (NVQ 3)",
                    "Certificate in Jewellery Manufacturing (NVQ 4)",
                ],
                "Diploma Level Courses": [],
                "International Courses": []
            },
            "Batticaloa": {
                "Certificate Level Courses": [
                    "Certificate in Jewellery Manufacturing"
                ],
                "Diploma Level Courses": [],
                "International Courses": []
            },
            "Jaffna": {
                "Certificate Level Courses": [
                    "Certificate in Jewellery Manufacturing",
                    "Certificate in Costume Jewellery Manufacturing"
                ],
                "Diploma Level Courses": [],
                "International Courses": []
            }
        };

        const courseFees = {
            "Certificate in Basic Gemmology": {
                reg: 2000,
                fee: 50000
            },
            "Certificate in Gemmology": {
                reg: 2000,
                fee: 70000
            },
            "Certificate in Blended Gemmology": {
                reg: 2000,
                fee: 70000
            },
            "Certificate in Gems and Jewellery Valuation and Marketing": {
                reg: 2000,
                fee: 20000
            },
            "Certificate in Geuda Heat Treatment": {
                reg: 2000,
                fee: 55000
            },
            "Certificate in Gem Cutting and Polishing (NVQ 3)": {
                reg: 2000,
                fee: 35000
            },
            "Certificate in Gem Cutting and Polishing (NVQ 4)": {
                reg: 2000,
                fee: 45000
            },
            "Certificate in Gem Cutting and Polishing (Weekend)": {
                reg: 2000,
                fee: 35000
            },
            "Certificate in Gem Cutting and Polishing (10 DAYS)": {
                reg: 2000,
                fee: 14000
            },
            "Gem Related Certificate in Tailor – Made Courses": {},
            "Certificate in Jewellery Designing (Manual)": {
                reg: 2000,
                fee: 43000
            },
            "Certificate in Jewellery Designing Technology (NVQ 4)": {
                reg: 2000,
                fee: 60000
            },
            "Certificate in Computer Aided Jewellery Designing and Manufacturing (CAD/CAM)": {
                reg: 2000,
                fee: 60000
            },
            "Certificate in Jewellery Manufacturing (NVQ 3)": {
                reg: 2000,
                fee: 20000
            },
            "Certificate in Jewellery Manufacturing (NVQ 4)": {
                reg: 2000,
                fee: 50000
            },
            "Certificate in Jewellery Stone Setting (NVQ 3)": {
                reg: 2000,
                fee: 20000
            },
            "Certificate in Jewellery Casting and Electro Plating": {
                reg: 2000,
                fee: 50000
            },
            "Certificate in Jewellery Assaying and Hallmarking": {
                reg: 2000,
                fee: 45000
            },
            "Jewellery Certificate in Tailor – Made Courses": {},
            "Certificate in Costume Jewellery Manufacturing": {
                reg: 2000,
                fee: 20000
            },
            "Diploma in Professional Gemmology (Dip. PGSL)": {
                reg: 5000,
                fee: 200000,
                deposit: 5000
            },
            "Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)/ Diploma in Professional Jewellery (Dip. PJSL)": {
                reg: 2500,
                fee: 125000,
                deposit: 5000
            },
            "Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)": {
                reg: 2500,
                fee: 125000,
                deposit: 5000
            },
            "Gem-A Foundation Course": {
                reg: 10000,
                fee: 589567.22
            },
            "Gem-A Diploma Course": {
                reg: 10000,
                fee: 926309.82
            },

            "Certificate in Jewellery Manufacturing": {
                reg: 2000,
                fee: 20000
            }
        };

        const centreSelect = document.getElementById('regional-centre');
        const typeSelect = document.getElementById('course-type');
        const courseSelect = document.getElementById('course');
        const coursePrice = document.getElementById('course-price');
        const regFeeInput = document.getElementById('reg-fee');
        const courseFeeInput = document.getElementById('course-fee');
        const refundableDepositInput = document.getElementById('refundable-deposit');
        const totalFeeInput = document.getElementById('total-fee');

        typeSelect.addEventListener('change', function() {
            const centre = centreSelect.value;
            const type = this.value;
            courseSelect.innerHTML = '<option value="">Select a course</option>';

            if (centre && type && courseData[centre] && courseData[centre][type]) {
                courseData[centre][type].forEach(course => {
                    courseSelect.innerHTML += `<option value="${course}">${course}</option>`;
                });
            }

            coursePrice.value = "";
            regFeeInput.value = "";
            courseFeeInput.value = "";
            refundableDepositInput.value = "0";
            totalFeeInput.value = "";
        });

        courseSelect.addEventListener('change', function() {
            const selectedCourse = this.value;
            const tailorMessageCourses = [
                "Gem Related Certificate in Tailor – Made Courses",
                "Jewellery Certificate in Tailor – Made Courses"
            ];

            if (tailorMessageCourses.includes(selectedCourse)) {
                coursePrice.value = "For further information such as course dates, duration, and payment details, please contact the administrator";
                regFeeInput.value = "";
                courseFeeInput.value = "";
                refundableDepositInput.value = "0";
                totalFeeInput.value = "";
            } else if (courseFees[selectedCourse]) {
                const { reg, fee, deposit } = courseFees[selectedCourse];
                const dep = deposit || 0;
                let priceText = `Registration Fee: Rs. ${reg.toLocaleString()} | Course Fee: Rs. ${fee.toLocaleString()}`;
                if (dep > 0) {
                    priceText += ` | Refundable Deposit: Rs. ${dep.toLocaleString()}`;
                }
                coursePrice.value = priceText;
                regFeeInput.value = reg;
                courseFeeInput.value = fee;
                refundableDepositInput.value = dep;
                updateTotalFee();
            } else {
                coursePrice.value = "";
                regFeeInput.value = "";
                courseFeeInput.value = "";
                refundableDepositInput.value = "0";
                totalFeeInput.value = "";
            }
        });

        const updateTotalFee = () => {
            const regFee = parseFloat(regFeeInput.value) || 0;
            const courseFee = parseFloat(courseFeeInput.value) || 0;
            const deposit = parseFloat(refundableDepositInput.value) || 0;
            const totalFee = regFee + courseFee + deposit;
            totalFeeInput.value = `Total Fee: Rs. ${totalFee.toLocaleString()}`;
        };
    </script>
</body>

</html>