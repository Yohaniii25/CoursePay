<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Application Form</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <div class="max-w-3xl mx-auto mt-10 bg-white shadow-lg rounded-xl p-8">
        <h1 class="text-2xl font-bold mb-6">Student Application Form</h1>


        <form action="submit.php" method="POST" enctype="multipart/form-data" class="space-y-5">


            <div>
                <label for="regional-centre" class="block font-medium">Regional Centre</label>
                <select id="regional-centre" name="regional_centre" required
                    class="mt-1 w-full px-4 py-2 border rounded-lg">
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
                <label for="course-type" class="block font-medium">Course Type</label>
                <select id="course-type" name="course_type" required class="mt-1 w-full px-4 py-2 border rounded-lg">
                    <option value="">Select a type</option>
                    <option value="Certificate Level Courses">Certificate Level Courses</option>
                    <option value="Diploma Level Course">Diploma Level Course</option>
                    <option value="International Courses">International Courses</option>
                </select>
            </div>


            <div>
                <label for="course" class="block font-medium">Course</label>
                <select id="course" name="course" required class="mt-1 w-full px-4 py-2 border rounded-lg">
                    <option value="">Select a course</option>
                </select>
            </div>


            <div>
                <label for="course-price" class="block font-medium">Course Fee</label>
                <input type="text" id="course-price" name="course_fee_display" readonly
                    class="mt-1 w-full px-4 py-2 border rounded-lg bg-gray-100">

                <input type="hidden" id="reg-fee" name="reg_fee">
                <input type="hidden" id="course-fee" name="course_fee">
            </div>

            <!-- show total fee -->
            <div>
                <label for="total-fee" class="block font-medium">Total Fee</label>
                <input type="text" id="total-fee" name="total_fee_display" readonly
                    class="mt-1 w-full px-4 py-2 border rounded-lg bg-gray-100">

            </div>


            <div>
                <label for="nic-passport" class="block font-medium">NIC No. / Passport No.</label>
                <input type="text" id="nic-passport" name="nic_passport" required
                    class="mt-1 w-full px-4 py-2 border rounded-lg">
            </div>


            <div>
                <label for="nic-file" class="block font-medium">Attach a copy of NIC / Passport</label>
                <input type="file" id="nic-file" name="nic_file" accept=".pdf,.jpg,.jpeg,.png" required
                    class="mt-1 w-full px-4 py-2 border rounded-lg">
            </div>


            <div>
                <label for="education-background" class="block font-medium">Educational Background</label>
                <textarea id="education-background" name="education_background" rows="3"
                    class="mt-1 w-full px-4 py-2 border rounded-lg"></textarea>
            </div>


            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="declaration" value="1" required class="mr-2">
                    I hereby declare that the above information is true and correct.
                </label>
            </div>


            <div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                    Submit Application
                </button>
            </div>
        </form>
    </div>
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
                "Certificate in Tailor – Made Course - Gem",
                "Certificate in Jewellery Designing (Manual)",
                "Certificate in Jewellery Designing Technology (NVQ 4)",
                "Certificate in Computer Aided Jewellery Designing and Manufacturing (CAD/CAM)",
                "Certificate in Jewellery Manufacturing (NVQ 3)",
                "Certificate in Jewellery Manufacturing (NVQ 4)",
                "Certificate in Jewellery Stone Setting (NVQ 3)",
                "Certificate in Jewellery Casting and Electro Plating",
                "Certificate in Jewellery Assaying and Hallmarking",
                "Certificate in Tailor – Made Course - Jewellery"
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
                "Certificate in Tailor – Made Course - Gem",
                "Certificate in Jewellery Designing (Manual)",
                "Certificate in Jewellery Designing Technology (NVQ 4)",
                "Certificate in Computer Aided Jewellery Designing and Manufacturing (CAD/CAM)",
                "Certificate in Jewellery Manufacturing (NVQ 3)",
                "Certificate in Jewellery Manufacturing (NVQ 4)",
                "Certificate in Costume Jewellery Manufacturing",
                "Certificate in Jewellery Stone Setting (NVQ 3)",
                "Certificate in Tailor – Made Course - Jewellery"
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
        "Certificate in Tailor – Made Course - Gem": {
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
        "Certificate in Tailor – Made Course - Jewellery": {
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
        // Add missing course for Batticaloa and Jaffna
        "Certificate in Jewellery Manufacturing": {
            reg: 2000,
            fee: 20000 // Assuming same as NVQ 3, as it's unspecified
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
        // Clear fees when course type changes
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
            updateTotalFee(); // Call to update total fee
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

</html>