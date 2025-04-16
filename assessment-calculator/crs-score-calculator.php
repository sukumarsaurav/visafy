<?php
$page_title = "CRS Score Calculator | CANEXT Immigration";
include('../includes/header.php');
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../images/assessment/crs-header.jpg'); background-size: cover; background-position: center; padding: 100px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
<div style="position: absolute; width: 300px; height: 300px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: -100px; left: -100px;"></div>
    <div style="position: absolute; width: 200px; height: 200px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); bottom: -50px; right: 10%; animation: pulse 4s infinite alternate;"></div>
    <div style="position: absolute; width: 100px; height: 100px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: 30%; right: 20%; animation: pulse 3s infinite alternate;"></div>
    <div class="container">
        <h1 data-aos="fade-up">Express Entry CRS Calculator</h1>
        <p data-aos="fade-up" data-aos-delay="100">Calculate your Comprehensive Ranking System (CRS) score for Express Entry</p>
    </div>
</section>

<!-- Calculator Section -->
<section class="section calculator-section" style="padding: 60px 0;">
    <div class="container" style="max-width: 800px; margin: 0 auto;">
        <!-- Progress Bar -->
        <div class="progress-container" style="margin-bottom: 30px;">
            <div class="progress-info" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span id="stepIndicator">Step 1 of 6</span>
                <span id="progressPercentage">16% Complete</span>
            </div>
            <div class="progress-bar-bg" style="background: #e0e0e0; height: 8px; border-radius: 4px;">
                <div id="progressBar" style="width: 16%; height: 100%; background-color: var(--color-burgundy); border-radius: 4px; transition: width 0.3s ease;"></div>
            </div>
        </div>

        <!-- Calculator Form -->
        <form id="crsCalculator" class="calculator-form" style="background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); overflow: hidden;">
            <!-- Steps Container -->
            <div id="calculatorSteps">
                <!-- Step content will be dynamically loaded here -->
            </div>
        </form>

        <!-- Results Section (initially hidden) -->
        <div id="resultsSection" style="display: none;">
            <!-- Results content will be dynamically loaded here -->
        </div>
    </div>
</section>

<script>
// Form data object to store all inputs
let formData = {
    maritalStatus: 'single',
    age: 30,
    education: '',
    firstLanguage: {
        speaking: '',
        listening: '',
        reading: '',
        writing: ''
    },
    secondLanguage: {
        speaking: '',
        listening: '',
        reading: '',
        writing: ''
    },
    canadianWorkExperience: '',
    spouseEducation: '',
    spouseLanguage: {
        speaking: '',
        listening: '',
        reading: '',
        writing: ''
    },
    spouseCanadianWorkExperience: '',
    foreignWorkExperience: '',
    certificateOfQualification: 'no',
    provincialNomination: 'no',
    jobOffer: 'no',
    canadianEducation: 'no',
    frenchLanguageSkills: 'no',
    sibling: 'no'
};

let currentStep = 1;
let totalSteps = 6;

// Initialize the calculator
document.addEventListener('DOMContentLoaded', function() {
    loadStep(1);
    updateProgress();
});

function loadStep(step) {
    const stepsContainer = document.getElementById('calculatorSteps');
    stepsContainer.innerHTML = createStep(step);
    
    // Add event listeners for the new step
    setupStepEventListeners(step);
}

function createStep(step) {
    switch(step) {
        case 1:
            return `
                <div class="step-content" style="padding: 30px;">
                    <h2 style="color: var(--color-burgundy); margin-bottom: 20px;">Personal Information</h2>
                    <p style="color: #666; margin-bottom: 30px;">Let's start with some basic information about you</p>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Marital Status</label>
                        <div class="radio-group" style="display: flex; flex-direction: column; gap: 10px;">
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="radio" name="maritalStatus" value="single" ${formData.maritalStatus === 'single' ? 'checked' : ''} style="margin-right: 10px;">
                                Single
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="radio" name="maritalStatus" value="married" ${formData.maritalStatus === 'married' ? 'checked' : ''} style="margin-right: 10px;">
                                Married or Common-Law Partner
                            </label>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Age: <span id="ageValue">${formData.age}</span></label>
                        <input type="range" id="age" name="age" min="18" max="65" value="${formData.age}" 
                               style="width: 100%; accent-color: var(--color-burgundy);">
                        <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                            <span style="color: #666;">18</span>
                            <span style="color: #666;">65</span>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Highest Level of Education</label>
                        <select name="education" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">Select your education level</option>
                            <option value="phd" ${formData.education === 'phd' ? 'selected' : ''}>Doctorate (PhD)</option>
                            <option value="masters" ${formData.education === 'masters' ? 'selected' : ''}>Master's Degree</option>
                            <option value="twoOrMore" ${formData.education === 'twoOrMore' ? 'selected' : ''}>Two or more post-secondary credentials (3+ years)</option>
                            <option value="bachelors" ${formData.education === 'bachelors' ? 'selected' : ''}>Bachelor's Degree</option>
                            <option value="diploma3" ${formData.education === 'diploma3' ? 'selected' : ''}>Post-secondary program (3+ years)</option>
                            <option value="diploma12" ${formData.education === 'diploma12' ? 'selected' : ''}>Post-secondary program (1-2 years)</option>
                            <option value="highschool" ${formData.education === 'highschool' ? 'selected' : ''}>High School</option>
                        </select>
                    </div>

                    <div class="navigation" style="display: flex; justify-content: flex-end; margin-top: 30px;">
                        <button type="button" onclick="nextStep()" class="btn" 
                                style="background-color: var(--color-burgundy); color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer;">
                            Next <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                        </button>
                    </div>
                </div>
            `;
        case 2:
            return `
                <div class="step-content" style="padding: 30px;">
                    <h2 style="color: var(--color-burgundy); margin-bottom: 20px;">Language Proficiency</h2>
                    <p style="color: #666; margin-bottom: 30px;">Enter your scores for your first official language (English or French)</p>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Speaking</label>
                        <select name="firstLanguageSpeaking" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">Select CLB level</option>
                            <option value="clb4" ${formData.firstLanguage.speaking === 'clb4' ? 'selected' : ''}>CLB 4 or less</option>
                            <option value="clb5" ${formData.firstLanguage.speaking === 'clb5' ? 'selected' : ''}>CLB 5</option>
                            <option value="clb6" ${formData.firstLanguage.speaking === 'clb6' ? 'selected' : ''}>CLB 6</option>
                            <option value="clb7" ${formData.firstLanguage.speaking === 'clb7' ? 'selected' : ''}>CLB 7</option>
                            <option value="clb8" ${formData.firstLanguage.speaking === 'clb8' ? 'selected' : ''}>CLB 8</option>
                            <option value="clb9" ${formData.firstLanguage.speaking === 'clb9' ? 'selected' : ''}>CLB 9</option>
                            <option value="clb10plus" ${formData.firstLanguage.speaking === 'clb10plus' ? 'selected' : ''}>CLB 10 or higher</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Listening</label>
                        <select name="firstLanguageListening" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">Select CLB level</option>
                            <option value="clb4" ${formData.firstLanguage.listening === 'clb4' ? 'selected' : ''}>CLB 4 or less</option>
                            <option value="clb5" ${formData.firstLanguage.listening === 'clb5' ? 'selected' : ''}>CLB 5</option>
                            <option value="clb6" ${formData.firstLanguage.listening === 'clb6' ? 'selected' : ''}>CLB 6</option>
                            <option value="clb7" ${formData.firstLanguage.listening === 'clb7' ? 'selected' : ''}>CLB 7</option>
                            <option value="clb8" ${formData.firstLanguage.listening === 'clb8' ? 'selected' : ''}>CLB 8</option>
                            <option value="clb9" ${formData.firstLanguage.listening === 'clb9' ? 'selected' : ''}>CLB 9</option>
                            <option value="clb10plus" ${formData.firstLanguage.listening === 'clb10plus' ? 'selected' : ''}>CLB 10 or higher</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Reading</label>
                        <select name="firstLanguageReading" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">Select CLB level</option>
                            <option value="clb4" ${formData.firstLanguage.reading === 'clb4' ? 'selected' : ''}>CLB 4 or less</option>
                            <option value="clb5" ${formData.firstLanguage.reading === 'clb5' ? 'selected' : ''}>CLB 5</option>
                            <option value="clb6" ${formData.firstLanguage.reading === 'clb6' ? 'selected' : ''}>CLB 6</option>
                            <option value="clb7" ${formData.firstLanguage.reading === 'clb7' ? 'selected' : ''}>CLB 7</option>
                            <option value="clb8" ${formData.firstLanguage.reading === 'clb8' ? 'selected' : ''}>CLB 8</option>
                            <option value="clb9" ${formData.firstLanguage.reading === 'clb9' ? 'selected' : ''}>CLB 9</option>
                            <option value="clb10plus" ${formData.firstLanguage.reading === 'clb10plus' ? 'selected' : ''}>CLB 10 or higher</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Writing</label>
                        <select name="firstLanguageWriting" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">Select CLB level</option>
                            <option value="clb4" ${formData.firstLanguage.writing === 'clb4' ? 'selected' : ''}>CLB 4 or less</option>
                            <option value="clb5" ${formData.firstLanguage.writing === 'clb5' ? 'selected' : ''}>CLB 5</option>
                            <option value="clb6" ${formData.firstLanguage.writing === 'clb6' ? 'selected' : ''}>CLB 6</option>
                            <option value="clb7" ${formData.firstLanguage.writing === 'clb7' ? 'selected' : ''}>CLB 7</option>
                            <option value="clb8" ${formData.firstLanguage.writing === 'clb8' ? 'selected' : ''}>CLB 8</option>
                            <option value="clb9" ${formData.firstLanguage.writing === 'clb9' ? 'selected' : ''}>CLB 9</option>
                            <option value="clb10plus" ${formData.firstLanguage.writing === 'clb10plus' ? 'selected' : ''}>CLB 10 or higher</option>
                        </select>
                    </div>

                    <div class="navigation" style="display: flex; justify-content: space-between; margin-top: 30px;">
                        <button type="button" onclick="prevStep()" class="btn" 
                                style="background-color: white; color: var(--color-burgundy); padding: 12px 30px; border: 1px solid var(--color-burgundy); border-radius: 5px; cursor: pointer;">
                            <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back
                        </button>
                        <button type="button" onclick="nextStep()" class="btn" 
                                style="background-color: var(--color-burgundy); color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer;">
                            Next <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                        </button>
                    </div>
                </div>
            `;
        case 3:
            return `
                <div class="step-content" style="padding: 30px;">
                    <h2 style="color: var(--color-burgundy); margin-bottom: 20px;">Work Experience</h2>
                    
                    <div class="form-group" style="margin-bottom: 30px;">
                        <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Canadian Work Experience</h3>
                        <label style="display: block; margin-bottom: 10px; color: #666;">How many years of skilled work experience do you have in Canada?</label>
                        <select name="canadianExperience" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">Select years of experience</option>
                            <option value="none" ${formData.canadianExperience === 'none' ? 'selected' : ''}>None or less than a year</option>
                            <option value="1year" ${formData.canadianExperience === '1year' ? 'selected' : ''}>1 year</option>
                            <option value="2years" ${formData.canadianExperience === '2years' ? 'selected' : ''}>2 years</option>
                            <option value="3years" ${formData.canadianExperience === '3years' ? 'selected' : ''}>3 years</option>
                            <option value="4years" ${formData.canadianExperience === '4years' ? 'selected' : ''}>4 years</option>
                            <option value="5years" ${formData.canadianExperience === '5years' ? 'selected' : ''}>5 years or more</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 30px;">
                        <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Foreign Work Experience</h3>
                        <label style="display: block; margin-bottom: 10px; color: #666;">How many years of foreign skilled work experience do you have?</label>
                        <select name="foreignExperience" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">Select years of experience</option>
                            <option value="none" ${formData.foreignExperience === 'none' ? 'selected' : ''}>None or less than a year</option>
                            <option value="1to2" ${formData.foreignExperience === '1to2' ? 'selected' : ''}>1-2 years</option>
                            <option value="3years" ${formData.foreignExperience === '3years' ? 'selected' : ''}>3 years or more</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 30px;">
                        <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Certificate of Qualification</h3>
                        <label style="display: block; margin-bottom: 10px; color: #666;">Do you have a certificate of qualification from a Canadian province/territory?</label>
                        <div style="display: flex; gap: 20px;">
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="radio" name="certificate" value="yes" 
                                    ${formData.certificate === 'yes' ? 'checked' : ''}
                                    style="margin-right: 8px;">
                                Yes
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="radio" name="certificate" value="no" 
                                    ${formData.certificate === 'no' ? 'checked' : ''}
                                    style="margin-right: 8px;">
                                No
                            </label>
                        </div>
                    </div>

                    <div class="navigation" style="display: flex; justify-content: space-between; margin-top: 30px;">
                        <button type="button" onclick="prevStep()" class="btn" 
                                style="background-color: white; color: var(--color-burgundy); padding: 12px 30px; border: 1px solid var(--color-burgundy); border-radius: 5px; cursor: pointer;">
                            <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back
                        </button>
                        <button type="button" onclick="nextStep()" class="btn" 
                                style="background-color: var(--color-burgundy); color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer;">
                            Next <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                        </button>
                    </div>
                </div>
            `;
        case 4:
            return `
                <div class="step-content" style="padding: 30px;">
                    <h2 style="color: var(--color-burgundy); margin-bottom: 20px;">Additional Factors</h2>
                    
                    <div class="form-group" style="margin-bottom: 30px;">
                        <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Provincial Nomination</h3>
                        <label style="display: block; margin-bottom: 10px; color: #666;">Do you have a provincial nomination?</label>
                        <div style="display: flex; gap: 20px;">
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="radio" name="provincialNomination" value="yes" 
                                    ${formData.provincialNomination === 'yes' ? 'checked' : ''}
                                    style="margin-right: 8px;">
                                Yes
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="radio" name="provincialNomination" value="no" 
                                    ${formData.provincialNomination === 'no' ? 'checked' : ''}
                                    style="margin-right: 8px;">
                                No
                            </label>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 30px;">
                        <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Job Offer</h3>
                        <label style="display: block; margin-bottom: 10px; color: #666;">Do you have a valid job offer from a Canadian employer?</label>
                        <select name="jobOffer" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">Select job offer status</option>
                            <option value="none" ${formData.jobOffer === 'none' ? 'selected' : ''}>No job offer</option>
                            <option value="noc00" ${formData.jobOffer === 'noc00' ? 'selected' : ''}>NOC 00</option>
                            <option value="nocAB" ${formData.jobOffer === 'nocAB' ? 'selected' : ''}>NOC 0, A, or B</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 30px;">
                        <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Education in Canada</h3>
                        <label style="display: block; margin-bottom: 10px; color: #666;">Have you completed any education in Canada?</label>
                        <select name="canadianEducation" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">Select education level</option>
                            <option value="none" ${formData.canadianEducation === 'none' ? 'selected' : ''}>None</option>
                            <option value="oneOrTwo" ${formData.canadianEducation === 'oneOrTwo' ? 'selected' : ''}>One or two-year diploma/certificate</option>
                            <option value="threeOrMore" ${formData.canadianEducation === 'threeOrMore' ? 'selected' : ''}>Three years or longer degree/diploma</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 30px;">
                        <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Sibling in Canada</h3>
                        <label style="display: block; margin-bottom: 10px; color: #666;">Do you have a sibling who is a Canadian citizen or permanent resident?</label>
                        <div style="display: flex; gap: 20px;">
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="radio" name="sibling" value="yes" 
                                    ${formData.sibling === 'yes' ? 'checked' : ''}
                                    style="margin-right: 8px;">
                                Yes
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="radio" name="sibling" value="no" 
                                    ${formData.sibling === 'no' ? 'checked' : ''}
                                    style="margin-right: 8px;">
                                No
                            </label>
                        </div>
                    </div>

                    <div class="navigation" style="display: flex; justify-content: space-between; margin-top: 30px;">
                        <button type="button" onclick="prevStep()" class="btn" 
                                style="background-color: white; color: var(--color-burgundy); padding: 12px 30px; border: 1px solid var(--color-burgundy); border-radius: 5px; cursor: pointer;">
                            <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back
                        </button>
                        <button type="button" onclick="calculateScore()" class="btn" 
                                style="background-color: var(--color-burgundy); color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer;">
                            Calculate CRS Score <i class="fas fa-calculator" style="margin-left: 8px;"></i>
                        </button>
                    </div>
                </div>
            `;
    }
}

function setupStepEventListeners(step) {
    if (step === 1) {
        // Age slider
        const ageSlider = document.getElementById('age');
        const ageValue = document.getElementById('ageValue');
        if (ageSlider && ageValue) {
            ageSlider.addEventListener('input', function() {
                ageValue.textContent = this.value;
                formData.age = parseInt(this.value);
            });
        }

        // Marital status
        const maritalInputs = document.getElementsByName('maritalStatus');
        maritalInputs.forEach(input => {
            input.addEventListener('change', function() {
                formData.maritalStatus = this.value;
                totalSteps = this.value === 'married' ? 6 : 5;
                updateProgress();
            });
        });

        // Education select
        const educationSelect = document.querySelector('select[name="education"]');
        if (educationSelect) {
            educationSelect.addEventListener('change', function() {
                formData.education = this.value;
            });
        }
    } else if (step === 2) {
        // Language proficiency selects
        const languageSelects = ['Speaking', 'Listening', 'Reading', 'Writing'];
        languageSelects.forEach(skill => {
            const select = document.querySelector(`select[name="firstLanguage${skill}"]`);
            if (select) {
                select.addEventListener('change', function() {
                    formData.firstLanguage[skill.toLowerCase()] = this.value;
                });
            }
        });
    } else if (step === 3) {
        // Work experience selects and radio buttons
        const experienceSelect = document.querySelector('select[name="canadianExperience"]');
        if (experienceSelect) {
            experienceSelect.addEventListener('change', function() {
                formData.canadianExperience = this.value;
            });
        }

        const foreignExperienceSelect = document.querySelector('select[name="foreignExperience"]');
        if (foreignExperienceSelect) {
            foreignExperienceSelect.addEventListener('change', function() {
                formData.foreignExperience = this.value;
            });
        }

        const certificateRadios = document.querySelectorAll('input[name="certificate"]');
        certificateRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                formData.certificate = this.value;
            });
        });
    } else if (step === 4) {
        // Additional factors
        const provincialNominationRadios = document.querySelectorAll('input[name="provincialNomination"]');
        provincialNominationRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                formData.provincialNomination = this.value;
            });
        });

        const jobOfferSelect = document.querySelector('select[name="jobOffer"]');
        if (jobOfferSelect) {
            jobOfferSelect.addEventListener('change', function() {
                formData.jobOffer = this.value;
            });
        }

        const canadianEducationSelect = document.querySelector('select[name="canadianEducation"]');
        if (canadianEducationSelect) {
            canadianEducationSelect.addEventListener('change', function() {
                formData.canadianEducation = this.value;
            });
        }

        const siblingRadios = document.querySelectorAll('input[name="sibling"]');
        siblingRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                formData.sibling = this.value;
            });
        });
    }
}

function updateProgress() {
    const progress = (currentStep / totalSteps) * 100;
    document.getElementById('progressBar').style.width = `${progress}%`;
    document.getElementById('progressPercentage').textContent = `${Math.round(progress)}% Complete`;
    document.getElementById('stepIndicator').textContent = `Step ${currentStep} of ${totalSteps}`;
}

function nextStep() {
    if (validateStep(currentStep)) {
        currentStep++;
        loadStep(currentStep);
        updateProgress();
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        loadStep(currentStep);
        updateProgress();
    }
}

function validateStep(step) {
    let isValid = true;
    let errorMessage = '';

    switch(step) {
        case 1:
            if (!formData.education) {
                isValid = false;
                errorMessage = 'Please select your education level';
            }
            break;
        case 2:
            const languageFields = ['speaking', 'listening', 'reading', 'writing'];
            for (const field of languageFields) {
                if (!formData.firstLanguage[field]) {
                    isValid = false;
                    errorMessage = 'Please complete all language proficiency fields';
                    break;
                }
            }
            break;
        case 3:
            if (!formData.canadianExperience) {
                isValid = false;
                errorMessage = 'Please select your Canadian work experience';
            } else if (!formData.foreignExperience) {
                isValid = false;
                errorMessage = 'Please select your foreign work experience';
            } else if (!formData.certificate) {
                isValid = false;
                errorMessage = 'Please indicate if you have a certificate of qualification';
            }
            break;
        case 4:
            if (!formData.provincialNomination) {
                isValid = false;
                errorMessage = 'Please indicate if you have a provincial nomination';
            } else if (!formData.jobOffer) {
                isValid = false;
                errorMessage = 'Please select your job offer status';
            } else if (!formData.canadianEducation) {
                isValid = false;
                errorMessage = 'Please select your Canadian education level';
            } else if (!formData.sibling) {
                isValid = false;
                errorMessage = 'Please indicate if you have a sibling in Canada';
            }
            break;
    }

    if (!isValid) {
        showError(errorMessage);
    }
    return isValid;
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.style.cssText = `
        background-color: #ffe6e6;
        color: var(--color-burgundy);
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
        text-align: center;
    `;
    errorDiv.textContent = message;

    const stepContent = document.querySelector('.step-content');
    stepContent.insertBefore(errorDiv, stepContent.firstChild);

    setTimeout(() => {
        errorDiv.remove();
    }, 3000);
}

function calculateScore() {
    if (!validateStep(4)) return;

    let score = 0;
    let strengths = [];
    let concerns = [];
    let recommendations = [];

    // Core/Human Capital Factors (max 500 points)
    // Age points (max 110)
    const age = parseInt(formData.age);
    if (age >= 18 && age <= 35) {
        score += 110;
        strengths.push('Your age is in the optimal range for immigration');
    } else if (age > 35 && age <= 45) {
        score += Math.max(0, 110 - ((age - 35) * 5));
        if (age > 40) {
            concerns.push('Age over 40 reduces CRS points');
            recommendations.push('Consider applying soon as age points decrease over time');
        }
    }

    // Education points (max 150)
    switch(formData.education) {
        case 'phd':
            score += 150;
            strengths.push('Doctoral degree provides maximum education points');
            break;
        case 'masters':
            score += 135;
            strengths.push('Master\'s degree provides significant education points');
            break;
        case 'bachelors':
            score += 120;
            break;
        case 'diploma3':
            score += 98;
            break;
        case 'diploma12':
            score += 90;
            break;
        default:
            score += 30;
            concerns.push('Lower education level reduces CRS points');
            recommendations.push('Consider upgrading your education to increase points');
    }

    // Language points (max 160)
    const languagePoints = calculateLanguagePoints(formData.firstLanguage);
    score += languagePoints;
    if (languagePoints >= 140) {
        strengths.push('Strong language skills significantly boost your score');
    } else if (languagePoints < 100) {
        concerns.push('Language scores could be improved');
        recommendations.push('Consider retaking language tests to improve scores');
    }

    // Work experience points (max 80)
    switch(formData.canadianExperience) {
        case '5plus':
            score += 80;
            strengths.push('Maximum points for Canadian work experience');
            break;
        case '3to5':
            score += 64;
            break;
        case '1to3':
            score += 40;
            break;
        default:
            concerns.push('Limited Canadian work experience');
            recommendations.push('Gaining Canadian work experience could increase your score');
    }

    // Additional points
    if (formData.provincialNomination === 'yes') {
        score += 600;
        strengths.push('Provincial nomination provides significant boost');
    }

    if (formData.jobOffer === 'noc00') {
        score += 200;
        strengths.push('Job offer in NOC 00 adds significant points');
    } else if (formData.jobOffer === 'nocAB') {
        score += 50;
        strengths.push('Job offer in NOC A or B adds points');
    }

    if (formData.canadianEducation === 'yes') {
        score += 30;
        strengths.push('Canadian education provides additional points');
    }

    if (formData.sibling === 'yes') {
        score += 15;
        strengths.push('Having a sibling in Canada adds points');
    }

    displayResults(score, strengths, concerns, recommendations);
}

function calculateLanguagePoints(languageScores) {
    let points = 0;
    const skillPoints = {
        'clb10plus': 32,
        'clb9': 29,
        'clb8': 26,
        'clb7': 23,
        'clb6': 21,
        'clb5': 19,
        'clb4': 17
    };

    for (const skill in languageScores) {
        points += skillPoints[languageScores[skill]] || 0;
    }

    return points;
}

function displayResults(score, strengths, concerns, recommendations) {
    const resultsSection = document.getElementById('resultsSection');
    const calculatorForm = document.getElementById('crsCalculator');
    
    // Hide the calculator form
    calculatorForm.style.display = 'none';
    
    // Show and populate results section
    resultsSection.style.display = 'block';
    resultsSection.innerHTML = `
        <div class="results-container" style="background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 30px; margin-top: 30px;">
            <div class="score-section" style="text-align: center; margin-bottom: 30px;">
                <h2 style="color: var(--color-burgundy); margin-bottom: 10px;">Your CRS Score</h2>
                <div class="score-circle" style="width: 150px; height: 150px; border-radius: 50%; background-color: var(--color-burgundy); color: white; display: flex; flex-direction: column; justify-content: center; align-items: center; margin: 0 auto;">
                    <span style="font-size: 2.5rem; font-weight: bold;">${score}</span>
                    <span style="font-size: 0.9rem;">points</span>
                </div>
            </div>

            <div class="analysis-section" style="margin-bottom: 30px;">
                <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Score Analysis</h3>
                
                ${strengths.length > 0 ? `
                    <div class="strengths" style="margin-bottom: 20px;">
                        <h4 style="color: #28a745; margin-bottom: 10px;">Strengths</h4>
                        <ul style="list-style: none; padding: 0;">
                            ${strengths.map(strength => `
                                <li style="display: flex; align-items: start; gap: 10px; margin-bottom: 8px;">
                                    <i class="fas fa-check-circle" style="color: #28a745; margin-top: 3px;"></i>
                                    <span>${strength}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                ` : ''}

                ${concerns.length > 0 ? `
                    <div class="concerns" style="margin-bottom: 20px;">
                        <h4 style="color: #dc3545; margin-bottom: 10px;">Areas for Improvement</h4>
                        <ul style="list-style: none; padding: 0;">
                            ${concerns.map(concern => `
                                <li style="display: flex; align-items: start; gap: 10px; margin-bottom: 8px;">
                                    <i class="fas fa-exclamation-circle" style="color: #dc3545; margin-top: 3px;"></i>
                                    <span>${concern}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                ` : ''}

                ${recommendations.length > 0 ? `
                    <div class="recommendations">
                        <h4 style="color: var(--color-burgundy); margin-bottom: 10px;">Recommendations</h4>
                        <ul style="list-style: none; padding: 0;">
                            ${recommendations.map(recommendation => `
                                <li style="display: flex; align-items: start; gap: 10px; margin-bottom: 8px;">
                                    <i class="fas fa-lightbulb" style="color: var(--color-burgundy); margin-top: 3px;"></i>
                                    <span>${recommendation}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                ` : ''}
            </div>

            <div class="action-buttons" style="display: flex; justify-content: center; gap: 20px; margin-top: 30px;">
                <button onclick="resetCalculator()" class="btn" 
                    style="background-color: white; color: var(--color-burgundy); padding: 12px 30px; border: 1px solid var(--color-burgundy); border-radius: 5px; cursor: pointer;">
                    Calculate Again
                </button>
                <a href="../consultant.php" class="btn" 
                    style="background-color: var(--color-burgundy); color: white; padding: 12px 30px; border: none; border-radius: 5px; text-decoration: none;">
                    Book a Consultation
                </a>
            </div>
        </div>
    `;
}

function resetCalculator() {
    // Reset form data
    formData = {
        maritalStatus: 'single',
        age: 30,
        education: '',
        firstLanguage: {
            speaking: '',
            listening: '',
            reading: '',
            writing: ''
        },
        secondLanguage: {
            speaking: '',
            listening: '',
            reading: '',
            writing: ''
        },
        canadianWorkExperience: '',
        spouseEducation: '',
        spouseLanguage: {
            speaking: '',
            listening: '',
            reading: '',
            writing: ''
        },
        spouseCanadianWorkExperience: '',
        foreignWorkExperience: '',
        certificateOfQualification: 'no',
        provincialNomination: 'no',
        jobOffer: 'no',
        canadianEducation: 'no',
        frenchLanguageSkills: 'no',
        sibling: 'no'
    };

    // Reset UI
    currentStep = 1;
    const resultsSection = document.getElementById('resultsSection');
    const calculatorForm = document.getElementById('crsCalculator');
    
    resultsSection.style.display = 'none';
    calculatorForm.style.display = 'block';
    
    loadStep(1);
    updateProgress();
}
</script>

<?php include('../includes/footer.php'); ?>
