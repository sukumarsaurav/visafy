<?php
$page_title = "Visa Eligibility Calculator | CANEXT Immigration";
include('../includes/header.php');
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../images/assessment/eligibility-header.jpg'); background-size: cover; background-position: center; padding: 100px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
<div style="position: absolute; width: 300px; height: 300px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: -100px; left: -100px;"></div>
    <div style="position: absolute; width: 200px; height: 200px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); bottom: -50px; right: 10%; animation: pulse 4s infinite alternate;"></div>
    <div style="position: absolute; width: 100px; height: 100px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: 30%; right: 20%; animation: pulse 3s infinite alternate;"></div>
    <div class="container">
        <h1 data-aos="fade-up">Visa Eligibility Calculator</h1>
        <p data-aos="fade-up" data-aos-delay="100">Answer a few questions to determine which Canadian immigration programs you may be eligible for</p>
    </div>
</section>

<!-- Calculator Section -->
<section class="section calculator-section" style="padding: 60px 0;">
    <div class="container" style="max-width: 800px; margin: 0 auto;">
        <!-- Progress Bar -->
        <div class="progress-container" style="margin-bottom: 30px;">
            <div class="progress-info" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span id="stepIndicator">Step 1 of 5</span>
                <span id="progressPercentage">20% Complete</span>
            </div>
            <div class="progress-bar" style="height: 8px; background-color: #e0e0e0; border-radius: 4px; overflow: hidden;">
                <div id="progressBar" style="width: 20%; height: 100%; background-color: var(--color-burgundy); transition: width 0.3s ease;"></div>
            </div>
        </div>

        <!-- Calculator Form -->
        <form id="eligibilityForm" class="calculator-form">
            <div id="formSteps">
                <!-- Steps will be dynamically loaded here -->
            </div>
        </form>
    </div>
</section>

<script>
// Initialize form data
let formData = {
    age: 30,
    education: '',
    workExperience: '',
    language: '',
    languageScore: '',
    canadaConnection: '',
    financialResources: ''
};

let currentStep = 1;
let progress = 20;

// Update progress bar and indicators
function updateProgress() {
    document.getElementById('stepIndicator').textContent = `Step ${currentStep} of 5`;
    document.getElementById('progressPercentage').textContent = `${progress}% Complete`;
    document.getElementById('progressBar').style.width = `${progress}%`;
}

// Render step content
function renderStep() {
    const formSteps = document.getElementById('formSteps');
    let stepHtml = '';

    switch(currentStep) {
        case 1:
            stepHtml = `
                <div class="step-card" style="background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px;">
                    <div class="card-header" style="margin-bottom: 24px;">
                        <h2 style="color: var(--color-burgundy); font-size: 1.5rem; margin-bottom: 8px;">Personal Information</h2>
                        <p style="color: #666;">Let's start with some basic information about you</p>
                    </div>
                    
                    <div class="card-content" style="margin-bottom: 24px;">
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label style="display: block; color: var(--color-burgundy); margin-bottom: 8px;">
                                Age: <span id="ageValue">${formData.age}</span>
                            </label>
                            <input type="range" id="age" min="18" max="65" value="${formData.age}"
                                style="width: 100%; margin-bottom: 8px;"
                                oninput="handleInputChange('age', this.value)">
                            <div style="display: flex; justify-content: space-between; color: #666;">
                                <span>18</span>
                                <span>65</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label style="display: block; color: var(--color-burgundy); margin-bottom: 8px;">
                                Highest Level of Education
                            </label>
                            <select id="education" 
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; color: #333;"
                                onchange="handleInputChange('education', this.value)">
                                <option value="">Select your education level</option>
                                <option value="phd">Doctorate (PhD)</option>
                                <option value="masters">Master's Degree</option>
                                <option value="bachelors">Bachelor's Degree</option>
                                <option value="diploma">Diploma or Certificate (1-2 years)</option>
                                <option value="highschool">High School</option>
                                <option value="none">Less than High School</option>
                            </select>
                        </div>
                    </div>

                    <div class="card-footer" style="display: flex; justify-content: flex-end;">
                        <button type="button" onclick="nextStep()"
                            style="background-color: var(--color-burgundy); color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            `;
            break;
        case 2:
            stepHtml = `
                <div class="step-card" style="background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px;">
                    <div class="card-header" style="margin-bottom: 24px;">
                        <h2 style="color: var(--color-burgundy); font-size: 1.5rem; margin-bottom: 8px;">Work Experience</h2>
                        <p style="color: #666;">Tell us about your work experience</p>
                    </div>
                    
                    <div class="card-content" style="margin-bottom: 24px;">
                        <div class="form-group">
                            <label style="display: block; color: var(--color-burgundy); margin-bottom: 16px;">
                                Years of Skilled Work Experience
                            </label>
                            <div class="radio-group" style="display: flex; flex-direction: column; gap: 12px;">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="workExperience" value="3plus" 
                                        ${formData.workExperience === '3plus' ? 'checked' : ''}
                                        onchange="handleInputChange('workExperience', this.value)">
                                    <span>3+ years</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="workExperience" value="1to3"
                                        ${formData.workExperience === '1to3' ? 'checked' : ''}
                                        onchange="handleInputChange('workExperience', this.value)">
                                    <span>1-3 years</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="workExperience" value="less1"
                                        ${formData.workExperience === 'less1' ? 'checked' : ''}
                                        onchange="handleInputChange('workExperience', this.value)">
                                    <span>Less than 1 year</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="workExperience" value="none"
                                        ${formData.workExperience === 'none' ? 'checked' : ''}
                                        onchange="handleInputChange('workExperience', this.value)">
                                    <span>No skilled work experience</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer" style="display: flex; justify-content: space-between;">
                        <button type="button" onclick="prevStep()"
                            style="background-color: white; color: var(--color-burgundy); padding: 10px 20px; border: 1px solid var(--color-burgundy); border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="button" onclick="nextStep()"
                            style="background-color: var(--color-burgundy); color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            `;
            break;
        case 3:
            stepHtml = `
                <div class="step-card" style="background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px;">
                    <div class="card-header" style="margin-bottom: 24px;">
                        <h2 style="color: var(--color-burgundy); font-size: 1.5rem; margin-bottom: 8px;">Language Proficiency</h2>
                        <p style="color: #666;">Tell us about your language skills</p>
                    </div>
                    
                    <div class="card-content" style="margin-bottom: 24px;">
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label style="display: block; color: var(--color-burgundy); margin-bottom: 16px;">
                                Primary Language Test
                            </label>
                            <div class="radio-group" style="display: flex; flex-direction: column; gap: 12px;">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="language" value="ielts"
                                        ${formData.language === 'ielts' ? 'checked' : ''}
                                        onchange="handleInputChange('language', this.value)">
                                    <span>IELTS (English)</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="language" value="celpip"
                                        ${formData.language === 'celpip' ? 'checked' : ''}
                                        onchange="handleInputChange('language', this.value)">
                                    <span>CELPIP (English)</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="language" value="tef"
                                        ${formData.language === 'tef' ? 'checked' : ''}
                                        onchange="handleInputChange('language', this.value)">
                                    <span>TEF Canada (French)</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="language" value="tcf"
                                        ${formData.language === 'tcf' ? 'checked' : ''}
                                        onchange="handleInputChange('language', this.value)">
                                    <span>TCF Canada (French)</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="language" value="none"
                                        ${formData.language === 'none' ? 'checked' : ''}
                                        onchange="handleInputChange('language', this.value)">
                                    <span>No language test taken</span>
                                </label>
                            </div>
                        </div>

                        <div id="languageProficiencySection" class="form-group" style="display: ${formData.language === 'none' ? 'none' : 'block'};">
                            <label style="display: block; color: var(--color-burgundy); margin-bottom: 16px;">
                                Language Proficiency Level
                            </label>
                            <div class="radio-group" style="display: flex; flex-direction: column; gap: 12px;">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="languageScore" value="clb9plus"
                                        ${formData.languageScore === 'clb9plus' ? 'checked' : ''}
                                        onchange="handleInputChange('languageScore', this.value)">
                                    <span>CLB 9+ (Advanced)</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="languageScore" value="clb7to8"
                                        ${formData.languageScore === 'clb7to8' ? 'checked' : ''}
                                        onchange="handleInputChange('languageScore', this.value)">
                                    <span>CLB 7-8 (Intermediate to Advanced)</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="languageScore" value="clb5to6"
                                        ${formData.languageScore === 'clb5to6' ? 'checked' : ''}
                                        onchange="handleInputChange('languageScore', this.value)">
                                    <span>CLB 5-6 (Intermediate)</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="languageScore" value="clb4less"
                                        ${formData.languageScore === 'clb4less' ? 'checked' : ''}
                                        onchange="handleInputChange('languageScore', this.value)">
                                    <span>CLB 4 or less (Basic)</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer" style="display: flex; justify-content: space-between;">
                        <button type="button" onclick="prevStep()"
                            style="background-color: white; color: var(--color-burgundy); padding: 10px 20px; border: 1px solid var(--color-burgundy); border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="button" onclick="nextStep()"
                            style="background-color: var(--color-burgundy); color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            `;
            break;
        case 4:
            stepHtml = `
                <div class="step-card" style="background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px;">
                    <div class="card-header" style="margin-bottom: 24px;">
                        <h2 style="color: var(--color-burgundy); font-size: 1.5rem; margin-bottom: 8px;">Additional Factors</h2>
                        <p style="color: #666;">A few more details to complete your assessment</p>
                    </div>
                    
                    <div class="card-content" style="margin-bottom: 24px;">
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label style="display: block; color: var(--color-burgundy); margin-bottom: 16px;">
                                Connections to Canada
                            </label>
                            <div class="radio-group" style="display: flex; flex-direction: column; gap: 12px;">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="canadaConnection" value="education"
                                        ${formData.canadaConnection === 'education' ? 'checked' : ''}
                                        onchange="handleInputChange('canadaConnection', this.value)">
                                    <span>Canadian education/degree</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="canadaConnection" value="work"
                                        ${formData.canadaConnection === 'work' ? 'checked' : ''}
                                        onchange="handleInputChange('canadaConnection', this.value)">
                                    <span>Canadian work experience</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="canadaConnection" value="family"
                                        ${formData.canadaConnection === 'family' ? 'checked' : ''}
                                        onchange="handleInputChange('canadaConnection', this.value)">
                                    <span>Close family member in Canada</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="canadaConnection" value="none"
                                        ${formData.canadaConnection === 'none' ? 'checked' : ''}
                                        onchange="handleInputChange('canadaConnection', this.value)">
                                    <span>No connections to Canada</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label style="display: block; color: var(--color-burgundy); margin-bottom: 16px;">
                                Financial Resources
                            </label>
                            <div class="radio-group" style="display: flex; flex-direction: column; gap: 12px;">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="financialResources" value="sufficient"
                                        ${formData.financialResources === 'sufficient' ? 'checked' : ''}
                                        onchange="handleInputChange('financialResources', this.value)">
                                    <span>Sufficient funds for settlement/study</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="financialResources" value="limited"
                                        ${formData.financialResources === 'limited' ? 'checked' : ''}
                                        onchange="handleInputChange('financialResources', this.value)">
                                    <span>Limited funds available</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="financialResources" value="unsure"
                                        ${formData.financialResources === 'unsure' ? 'checked' : ''}
                                        onchange="handleInputChange('financialResources', this.value)">
                                    <span>Not sure/prefer not to say</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer" style="display: flex; justify-content: space-between;">
                        <button type="button" onclick="prevStep()"
                            style="background-color: white; color: var(--color-burgundy); padding: 10px 20px; border: 1px solid var(--color-burgundy); border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="button" onclick="calculateResults()"
                            style="background-color: var(--color-burgundy); color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            Calculate Eligibility <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            `;
            break;
        // Additional cases will be added for other steps
    }

    formSteps.innerHTML = stepHtml;

    // Set current values
    if (currentStep === 1) {
        document.getElementById('education').value = formData.education;
    }
}

// Handle input changes
function handleInputChange(field, value) {
    formData[field] = value;
    if (field === 'age') {
        document.getElementById('ageValue').textContent = value;
    }
    if (field === 'language') {
        const proficiencySection = document.getElementById('languageProficiencySection');
        if (value === 'none') {
            proficiencySection.style.display = 'none';
            formData.languageScore = ''; // Clear language score when no test is selected
        } else {
            proficiencySection.style.display = 'block';
        }
    }
}

// Navigation functions
function nextStep() {
    if (validateStep()) {
        currentStep++;
        progress = currentStep * 20;
        updateProgress();
        renderStep();
        window.scrollTo(0, 0);
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        progress = currentStep * 20;
        updateProgress();
        renderStep();
        window.scrollTo(0, 0);
    }
}

// Validation function
function validateStep() {
    let isValid = true;
    let errorMessage = '';

    switch(currentStep) {
        case 1:
            if (!formData.education) {
                isValid = false;
                errorMessage = 'Please select your education level';
            }
            break;
        case 2:
            if (!formData.workExperience) {
                isValid = false;
                errorMessage = 'Please select your work experience';
            }
            break;
        case 3:
            if (!formData.language) {
                isValid = false;
                errorMessage = 'Please select your language test';
            } else if (formData.language !== 'none' && !formData.languageScore) {
                isValid = false;
                errorMessage = 'Please select your language proficiency level';
            }
            break;
        case 4:
            if (!formData.canadaConnection) {
                isValid = false;
                errorMessage = 'Please select your connection to Canada';
            } else if (!formData.financialResources) {
                isValid = false;
                errorMessage = 'Please select your financial resources';
            }
            break;
        // Additional validation cases will be added
    }

    if (!isValid) {
        alert(errorMessage);
    }
    return isValid;
}

// Add calculation functions
function calculateExpressEntryScore() {
    let score = 0;

    // Age points (simplified)
    const age = Number.parseInt(formData.age);
    if (age >= 20 && age <= 29) score += 100;
    else if (age >= 30 && age <= 44) score += 75;
    else score += 25;

    // Education points
    if (formData.education === 'phd') score += 140;
    else if (formData.education === 'masters') score += 135;
    else if (formData.education === 'bachelors') score += 120;
    else if (formData.education === 'diploma') score += 90;
    else score += 30;

    // Work experience
    if (formData.workExperience === '3plus') score += 80;
    else if (formData.workExperience === '1to3') score += 60;
    else if (formData.workExperience === 'less1') score += 40;
    else score += 0;

    // Language proficiency
    if (formData.languageScore === 'clb9plus') score += 130;
    else if (formData.languageScore === 'clb7to8') score += 100;
    else if (formData.languageScore === 'clb5to6') score += 70;
    else score += 30;

    // Canadian connections
    if (formData.canadaConnection === 'education') score += 30;
    else if (formData.canadaConnection === 'work') score += 50;
    else if (formData.canadaConnection === 'family') score += 15;
    else score += 0;

    return score;
}

function calculateResults() {
    if (!validateStep()) return;

    const score = calculateExpressEntryScore();
    const resultsHtml = `
        <div class="results-container" style="background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; max-width: 800px; margin: 0 auto;">
            <div class="results-header" style="text-align: center; margin-bottom: 30px;">
                <h2 style="color: var(--color-burgundy); font-size: 2rem; margin-bottom: 10px;">Your Immigration Eligibility Results</h2>
                <p style="color: #666;">Based on the information you provided</p>
            </div>

            <div class="score-circle" style="width: 200px; height: 200px; border-radius: 50%; border: 10px solid var(--color-burgundy); margin: 0 auto 30px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                <div style="font-size: 2.5rem; color: var(--color-burgundy); font-weight: bold;">${score}</div>
                <div style="color: #666;">Points</div>
            </div>

            <div class="eligibility-summary" style="margin-bottom: 30px;">
                <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Program Eligibility</h3>
                ${generateEligibilitySummary(score)}
            </div>

            <div class="recommendations" style="margin-bottom: 30px;">
                <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Next Steps</h3>
                <ul style="list-style: none; padding: 0;">
                    ${generateRecommendations(score)}
                </ul>
            </div>

            <div class="action-buttons" style="display: flex; justify-content: center; gap: 20px;">
                <button onclick="resetCalculator()" 
                    style="background-color: white; color: var(--color-burgundy); padding: 12px 24px; border: 1px solid var(--color-burgundy); border-radius: 5px; cursor: pointer;">
                    Start Over
                </button>
                <a href="../consultant.php" 
                    style="background-color: var(--color-burgundy); color: white; padding: 12px 24px; border: none; border-radius: 5px; text-decoration: none; display: inline-block;">
                    Book a Consultation
                </a>
            </div>
        </div>
    `;

    document.getElementById('formSteps').innerHTML = resultsHtml;
}

function generateEligibilitySummary(score) {
    let summary = '<div style="display: flex; flex-direction: column; gap: 15px;">';
    
    if (score >= 450) {
        summary += `
            <div style="display: flex; align-items: center; gap: 10px; color: #28a745;">
                <i class="fas fa-check-circle"></i>
                <span>Express Entry: Strong Candidate</span>
            </div>`;
    }
    
    if (formData.workExperience === '3plus' && formData.languageScore === 'clb7to8') {
        summary += `
            <div style="display: flex; align-items: center; gap: 10px; color: #28a745;">
                <i class="fas fa-check-circle"></i>
                <span>Canadian Experience Class: Eligible</span>
            </div>`;
    }
    
    if (formData.education === 'masters' || formData.education === 'phd') {
        summary += `
            <div style="display: flex; align-items: center; gap: 10px; color: #28a745;">
                <i class="fas fa-check-circle"></i>
                <span>Federal Skilled Worker: Potential Candidate</span>
            </div>`;
    }
    
    if (summary === '<div style="display: flex; flex-direction: column; gap: 15px;">') {
        summary += `
            <div style="display: flex; align-items: center; gap: 10px; color: #dc3545;">
                <i class="fas fa-exclamation-circle"></i>
                <span>Currently not eligible for Express Entry programs</span>
            </div>`;
    }
    
    return summary + '</div>';
}

function generateRecommendations(score) {
    let recommendations = '';
    
    if (score < 450) {
        recommendations += `
            <li style="margin-bottom: 15px; display: flex; align-items: start; gap: 10px;">
                <i class="fas fa-lightbulb" style="color: var(--color-burgundy); margin-top: 3px;"></i>
                <span>Consider improving your language scores to increase your chances</span>
            </li>`;
    }
    
    if (formData.workExperience === 'none' || formData.workExperience === 'less1') {
        recommendations += `
            <li style="margin-bottom: 15px; display: flex; align-items: start; gap: 10px;">
                <i class="fas fa-lightbulb" style="color: var(--color-burgundy); margin-top: 3px;"></i>
                <span>Gain more work experience in your field to qualify for additional programs</span>
            </li>`;
    }
    
    if (formData.canadaConnection === 'none') {
        recommendations += `
            <li style="margin-bottom: 15px; display: flex; align-items: start; gap: 10px;">
                <i class="fas fa-lightbulb" style="color: var(--color-burgundy); margin-top: 3px;"></i>
                <span>Explore provincial nomination programs for additional immigration pathways</span>
            </li>`;
    }
    
    recommendations += `
        <li style="margin-bottom: 15px; display: flex; align-items: start; gap: 10px;">
            <i class="fas fa-lightbulb" style="color: var(--color-burgundy); margin-top: 3px;"></i>
            <span>Book a consultation with our immigration experts for personalized guidance</span>
        </li>`;
    
    return recommendations;
}

function resetCalculator() {
    formData = {
        age: '30',
        education: '',
        workExperience: '',
        language: '',
        languageScore: '',
        canadaConnection: '',
        financialResources: ''
    };
    currentStep = 1;
    document.getElementById('formSteps').innerHTML = '';
    renderStep();
    updateProgress();
}

// Initialize first step
document.addEventListener('DOMContentLoaded', () => {
    renderStep();
    updateProgress();
});

</script>

<?php include('../includes/footer.php'); ?>
