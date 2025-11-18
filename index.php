<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Application Form</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">


<header class="bg-white shadow-md py-4 px-6 flex items-center justify-between relative">

    <div class="flex items-center">
        <img src="https://sltdigital.site/gem/wp-content/uploads/2025/06/GJRT-1.png"
            alt="Gem and Jewellery Research and Training Institute Logo"
            class="h-20 w-auto">
    </div>


    <h1 class="text-xl font-semibold text-indigo-800 text-center absolute left-1/2 transform -translate-x-1/2">
        Gem and Jewellery Research and Training Institute
    </h1>

  
    <a href="https://sltdigital.site/gem/"
        class="bg-[#25116F] text-white px-5 py-2 rounded-lg hover:opacity-90 transition">
        ← Back to Website
    </a>
</header>



    <div class="max-w-3xl mx-auto mt-10 bg-white shadow-lg rounded-xl p-8">
        <h1 class="text-2xl font-bold mb-6">Student Application Form</h1>


        <form action="submit.php" method="POST" enctype="multipart/form-data" class="max-w-3xl mx-auto p-8 bg-white rounded-xl shadow-lg space-y-6">

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 outline-none">
            </div>

            <div>
                <label for="contact-number" class="block text-sm font-semibold text-gray-700 mb-2">
                    Contact Number <span class="text-red-500">*</span>
                </label>
                <input type="text" id="contact-number" name="contact_number" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 outline-none">
            </div>

            <div>
                <label for="gmail" class="block text-sm font-semibold text-gray-700 mb-2">
                    Email
                </label>
                <input type="email" id="gmail" name="gmail"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 outline-none">
            </div>


            <div>
                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                    Address
                </label>
                <textarea id="address" name="address" rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 outline-none resize-none"></textarea>
            </div>

            <div>
                <label for="regional-centre" class="block text-sm font-semibold text-gray-700 mb-2">
                    Regional Centre <span class="text-red-500">*</span>
                </label>
                <select id="regional-centre" name="regional_centre" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 outline-none bg-white cursor-pointer">
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


            <div>
                <label for="course-type" class="block text-sm font-semibold text-gray-700 mb-2">
                    Course Type <span class="text-red-500">*</span>
                </label>
                <select id="course-type" name="course_type" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 outline-none bg-white cursor-pointer">
                    <option value="">Select a type</option>
                    <option value="Certificate Level Courses">Certificate Level Courses</option>
                    <option value="Diploma Level Courses">Diploma Level Courses</option>
                    <option value="International Courses">International Courses</option>
                </select>
            </div>


            <div>
                <label for="course" class="block text-sm font-semibold text-gray-700 mb-2">
                    Course <span class="text-red-500">*</span>
                </label>
                <select id="course" name="course" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 outline-none bg-white cursor-pointer">
                    <option value="">Select a course</option>
                </select>
            </div>


            <div>
                <label for="course-price" class="block text-sm font-semibold text-gray-700 mb-2">
                    Course Fee
                </label>
                <input type="text" id="course-price" name="course_fee_display" readonly
                    class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed">
                <input type="hidden" id="reg-fee" name="reg_fee">
                <input type="hidden" id="course-fee" name="course_fee">
            </div>

            <div>
                <label for="total-fee" class="block text-sm font-semibold text-gray-700 mb-2">
                    Total Fee
                </label>
                <input type="text" id="total-fee" name="total_fee_display" readonly
                    class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-600 font-semibold cursor-not-allowed">
            </div>

            <div>
                <label for="nic-passport" class="block text-sm font-semibold text-gray-700 mb-2">
                    NIC No. / Passport No. <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nic-passport" name="nic_passport" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 outline-none">
            </div>


            <div>
                <label for="nic-file" class="block text-sm font-semibold text-gray-700 mb-2">
                    Attach a copy of NIC / Passport <span class="text-red-500">*</span>
                </label>
                <input type="file" id="nic-file" name="nic_file" accept=".pdf,.jpg,.jpeg,.png" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 file:cursor-pointer">
            </div>


            <div>
                <label for="education-background" class="block text-sm font-semibold text-gray-700 mb-2">
                    Educational Background
                </label>
                <textarea id="education-background" name="education_background" rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 outline-none resize-none"></textarea>
            </div>


            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                <label class="flex items-start cursor-pointer">
                    <input type="checkbox" name="declaration" value="1" required
                        class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer">
                    <span class="ml-3 text-sm text-gray-700">
                        I hereby declare that the above information is true and correct.
                    </span>
                </label>
            </div>


            <div class="pt-4">
                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold py-3 px-6 rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 transform transition duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-md">
                    Submit Application
                </button>
            </div>
        </form>
    </div>

    <footer class="bg-black text-white text-sm py-4 text-left mt-10">
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
            "Gem Related Certificate in Tailor – Made Courses": {
                reg: 2000,
                fee: 50000
            },
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
            "Jewellery Certificate in Tailor – Made Courses": {
                reg: 2000,
                fee: 50000
            },
            "Certificate in Costume Jewellery Manufacturing": {
                reg: 2000,
                fee: 20000
            },
            "Diploma in Professional Gemmology (Dip. PGSL)": {
                reg: 2000,
                fee: 150000
            },
            "Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)/ Diploma in Professional Jewellery (Dip. PJSL)": {
                reg: 2000,
                fee: 125000
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
            totalFeeInput.value = "";
        });

        courseSelect.addEventListener('change', function() {
            const selectedCourse = this.value;
            if (courseFees[selectedCourse]) {
                const {
                    reg,
                    fee
                } = courseFees[selectedCourse];
                coursePrice.value =
                    `Registration Fee: Rs. ${reg.toLocaleString()} | Course Fee: Rs. ${fee.toLocaleString()}`;
                regFeeInput.value = reg;
                courseFeeInput.value = fee;
                updateTotalFee();
            } else {
                coursePrice.value = "";
                regFeeInput.value = "";
                courseFeeInput.value = "";
                totalFeeInput.value = "";
            }
        });

        const updateTotalFee = () => {
            const regFee = parseFloat(regFeeInput.value) || 0;
            const courseFee = parseFloat(courseFeeInput.value) || 0;
            const totalFee = regFee + courseFee;
            totalFeeInput.value = `Total Fee: Rs. ${totalFee.toLocaleString()}`;
        };
    </script>
</body>

<?php
session_start();
if (isset($_SESSION['payment_success'])) {
    $s = $_SESSION['payment_success'];
    unset($_SESSION['payment_success']); // Show only once
?>
<div class="max-w-3xl mx-auto bg-green-50 border-l-4 border-green-500 rounded-lg p-6 mb-8">
    <h2 class="text-xl font-bold text-green-700 mb-3">Payment Completed Successfully!</h2>
    <p class="text-gray-700"><strong>Student:</strong> <?php echo htmlspecialchars($s['name']); ?></p>
    <p class="text-gray-700"><strong>Reference No:</strong> <?php echo htmlspecialchars($s['ref']); ?></p>
    <p class="text-gray-700"><strong>Course:</strong> <?php echo htmlspecialchars($s['course']); ?></p>
    <p class="text-gray-700"><strong>Total Paid:</strong> Rs. <?php echo number_format($s['paid'], 2); ?></p>
    <p class="text-gray-700"><strong>Remaining:</strong> Rs. <?php echo number_format($s['due'], 2); ?></p>
    <?php if ($s['due'] <= 0): ?>
        <p class="text-green-600 font-semibold mt-2">Full payment received. You're all set!</p>
    <?php endif; ?>
    <button onclick="window.print()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
        Print Receipt
    </button>
</div>
<?php } ?>

<!-- Your existing index.php content continues below -->

</html>