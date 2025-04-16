<?php
$page_title = "Study Permit Eligibility Checker | CANEXT Immigration";
include('../includes/header.php');
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../images/assessment/study-permit-header.jpg'); background-size: cover; background-position: center; padding: 100px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
<div style="position: absolute; width: 300px; height: 300px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: -100px; left: -100px;"></div>
    <div style="position: absolute; width: 200px; height: 200px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); bottom: -50px; right: 10%; animation: pulse 4s infinite alternate;"></div>
    <div style="position: absolute; width: 100px; height: 100px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: 30%; right: 20%; animation: pulse 3s infinite alternate;"></div>
    <div class="container">
        <h1 data-aos="fade-up">Study Permit Eligibility Checker</h1>
        <p data-aos="fade-up" data-aos-delay="100" style="max-width: 700px; margin: 20px auto 0;">Assess your eligibility for a Canadian study permit with our comprehensive evaluation tool</p>
    </div>
</section>

<!-- Progress Bar -->
<div class="progress-container" style="background: var(--color-cream); padding: 20px 0;">
    <div class="container">
        <div class="progress" style="height: 8px; background-color: #e9ecef; border-radius: 4px; margin-bottom: 10px;">
            <div id="progress-bar" class="progress-bar" style="width: 20%; height: 100%; border-radius: 4px; background-color: var(--color-burgundy); transition: width 0.3s ease;"></div>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span style="color: var(--color-burgundy);">Step <span id="current-step">1</span> of 5</span>
            <span style="color: var(--color-burgundy);"><span id="progress-percentage">20</span>% Complete</span>
        </div>
    </div>
</div>

<!-- Assessment Form -->
<section class="section assessment-section" style="padding: 60px 0;">
    <div class="container">
        <form id="studyPermitForm" method="POST" style="max-width: 800px; margin: 0 auto;">
            <!-- Step 1: Academic Information -->
            <div class="step" id="step1">
                <div class="card" style="background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 30px;">
                    <h2 style="color: var(--color-burgundy); margin-bottom: 20px;">Academic Information</h2>
                    <p style="color: var(--color-dark); margin-bottom: 30px;">Tell us about your academic plans in Canada</p>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Do you have an acceptance letter from a designated learning institution (DLI) in Canada?</label>
                        <div class="radio-group" style="display: flex; flex-direction: column; gap: 10px;">
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="acceptanceLetter" value="yes" required>
                                <span>Yes</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="acceptanceLetter" value="no">
                                <span>No</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="acceptanceLetter" value="pending">
                                <span>Application submitted, waiting for response</span>
                            </label>
                        </div>
                    </div>
                    
                    <div id="acceptanceDetails" style="display: none;">
                        <div class="form-group" style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Name of Institution</label>
                            <input type="text" name="institution" placeholder="e.g., University of Toronto" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Program of Study</label>
                            <input type="text" name="program" placeholder="e.g., Master of Business Administration" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Program Length</label>
                            <select name="programLength" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                                <option value="">Select program length</option>
                                <option value="lessThan1">Less than 1 year</option>
                                <option value="1to2">1-2 years</option>
                                <option value="2to4">2-4 years</option>
                                <option value="moreThan4">More than 4 years</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="button-group" style="display: flex; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" onclick="nextStep()" class="btn" style="background-color: var(--color-burgundy); color: var(--color-light); padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer;">
                        Next <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
                    </button>
                </div>
            </div>

            <!-- Additional steps will be added dynamically via JavaScript -->
        </form>
    </div>
</section>

<script>
let currentStep = 1;
const totalSteps = 5;
let formData = {};

// Show/hide acceptance details based on radio selection
document.querySelectorAll('input[name="acceptanceLetter"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const acceptanceDetails = document.getElementById('acceptanceDetails');
        acceptanceDetails.style.display = this.value === 'yes' ? 'block' : 'none';
    });
});

function updateProgress() {
    const progress = (currentStep / totalSteps) * 100;
    document.getElementById('progress-bar').style.width = `${progress}%`;
    document.getElementById('current-step').textContent = currentStep;
    document.getElementById('progress-percentage').textContent = Math.round(progress);
}

function nextStep() {
    // Validate current step
    const currentStepElement = document.getElementById(`step${currentStep}`);
    const inputs = currentStepElement.querySelectorAll('input[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value) {
            isValid = false;
            input.style.borderColor = '#dc3545';
        } else {
            input.style.borderColor = '#ddd';
        }
    });

    if (!isValid) {
        alert('Please fill in all required fields');
        return;
    }

    // Save form data
    const formElements = currentStepElement.querySelectorAll('input, select');
    formElements.forEach(element => {
        if (element.type === 'radio') {
            if (element.checked) {
                formData[element.name] = element.value;
            }
        } else {
            formData[element.name] = element.value;
        }
    });

    // If it's the last step, calculate results
    if (currentStep === totalSteps) {
        calculateResults();
        return;
    }

    // Load next step
    currentStep++;
    loadStep(currentStep);
    updateProgress();
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        loadStep(currentStep);
        updateProgress();
    }
}

function loadStep(step) {
    // Hide all steps
    document.querySelectorAll('.step').forEach(el => el.style.display = 'none');
    
    // Create and show the requested step if it doesn't exist
    let stepElement = document.getElementById(`step${step}`);
    if (!stepElement) {
        stepElement = createStep(step);
        document.getElementById('studyPermitForm').appendChild(stepElement);
    }
    
    stepElement.style.display = 'block';
}

function createStep(step) {
    const stepElement = document.createElement('div');
    stepElement.id = `step${step}`;
    stepElement.className = 'step';
    
    let content = '';
    
    switch(step) {
        case 2:
            content = `
                <div class="card" style="background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 30px;">
                    <h2 style="color: var(--color-burgundy); margin-bottom: 20px;">Financial Information</h2>
                    <p style="color: var(--color-dark); margin-bottom: 30px;">Tell us about your financial situation</p>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Have you paid your tuition fees?</label>
                        <div class="radio-group" style="display: flex; flex-direction: column; gap: 10px;">
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="tuitionPaid" value="full" required>
                                <span>Yes, full tuition</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="tuitionPaid" value="partial">
                                <span>Yes, partial tuition</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="tuitionPaid" value="no">
                                <span>No, not yet</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Do you have sufficient financial support for your studies and stay in Canada?</label>
                        <div class="radio-group" style="display: flex; flex-direction: column; gap: 10px;">
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="financialSupport" value="sufficient" required>
                                <span>Yes, I have sufficient funds for tuition, living expenses, and return transportation</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="financialSupport" value="partial">
                                <span>I have partial funds and will be working part-time in Canada</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="financialSupport" value="insufficient">
                                <span>I'm not sure if my funds are sufficient</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">What is your source of financial support?</label>
                        <select name="financialAmount" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">Select source of funds</option>
                            <option value="personal">Personal savings</option>
                            <option value="family">Family support</option>
                            <option value="scholarship">Scholarship or grant</option>
                            <option value="loan">Education loan</option>
                            <option value="combination">Combination of sources</option>
                        </select>
                    </div>
                </div>
            `;
            break;
            
        case 3:
            content = `
                <div class="card" style="background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 30px;">
                    <h2 style="color: var(--color-burgundy); margin-bottom: 20px;">Immigration History</h2>
                    <p style="color: var(--color-dark); margin-bottom: 30px;">Tell us about your travel and immigration history</p>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">What is your travel history?</label>
                        <div class="radio-group" style="display: flex; flex-direction: column; gap: 10px;">
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="travelHistory" value="extensive" required>
                                <span>Extensive (traveled to multiple countries)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="travelHistory" value="limited">
                                <span>Limited (traveled to a few countries)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="travelHistory" value="none">
                                <span>None (never traveled internationally)</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Have you had any previous visa refusals?</label>
                        <div class="radio-group" style="display: flex; flex-direction: column; gap: 10px;">
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="previousRefusals" value="none" required>
                                <span>No previous refusals</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="previousRefusals" value="nonCanada">
                                <span>Yes, from countries other than Canada</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="previousRefusals" value="canada">
                                <span>Yes, from Canada</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Do you have family members in Canada?</label>
                        <div class="radio-group" style="display: flex; flex-direction: column; gap: 10px;">
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="familyInCanada" value="yes" required>
                                <span>Yes</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="familyInCanada" value="no">
                                <span>No</span>
                            </label>
                        </div>
                    </div>
                </div>
            `;
            break;
            
        case 4:
            content = `
                <div class="card" style="background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 30px;">
                    <h2 style="color: var(--color-burgundy); margin-bottom: 20px;">Additional Information</h2>
                    <p style="color: var(--color-dark); margin-bottom: 30px;">A few more details to complete your assessment</p>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Do you have ties to your home country that demonstrate you will return after your studies?</label>
                        <div class="radio-group" style="display: flex; flex-direction: column; gap: 10px;">
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="returnTies" value="strong" required>
                                <span>Strong ties (property, business, family, employment)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="returnTies" value="moderate">
                                <span>Moderate ties</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="returnTies" value="weak">
                                <span>Limited ties</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">What is your level of English or French proficiency?</label>
                        <div class="radio-group" style="display: flex; flex-direction: column; gap: 10px;">
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="englishFrenchProficiency" value="advanced" required>
                                <span>Advanced (CLB 7+)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="englishFrenchProficiency" value="intermediate">
                                <span>Intermediate (CLB 5-6)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="englishFrenchProficiency" value="basic">
                                <span>Basic (CLB 4 or less)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="englishFrenchProficiency" value="none">
                                <span>No formal test taken</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Have you undergone a medical examination for immigration purposes in the last 12 months?</label>
                        <div class="radio-group" style="display: flex; flex-direction: column; gap: 10px;">
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="medicalExam" value="yes" required>
                                <span>Yes</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="medicalExam" value="no">
                                <span>No</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Do you have a criminal record?</label>
                        <div class="radio-group" style="display: flex; flex-direction: column; gap: 10px;">
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="criminalRecord" value="yes" required>
                                <span>Yes</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="criminalRecord" value="no">
                                <span>No</span>
                            </label>
                        </div>
                    </div>
                </div>
            `;
            break;
            
        case 5:
            content = `
                <div class="card" style="background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 30px;">
                    <h2 style="color: var(--color-burgundy); margin-bottom: 20px;">Study Plan</h2>
                    <p style="color: var(--color-dark); margin-bottom: 30px;">Tell us about your study plan and future goals</p>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; color: var(--color-burgundy); font-weight: 500;">Do you have a detailed study plan explaining why you chose this program and institution?</label>
                        <div class="radio-group" style="display: flex; flex-direction: column; gap: 10px;">
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="studyPlan" value="detailed" required>
                                <span>Yes, I have a detailed plan explaining my program choice and career goals</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="studyPlan" value="basic">
                                <span>I have a basic plan but need to develop it further</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="studyPlan" value="no">
                                <span>No, I haven't prepared a study plan yet</span>
                            </label>
                        </div>
                    </div>
                </div>
            `;
            break;
    }
    
    // Add navigation buttons
    content += `
        <div class="button-group" style="display: flex; justify-content: space-between; margin-top: 20px;">
            <button type="button" onclick="prevStep()" class="btn" style="background-color: transparent; color: var(--color-burgundy); padding: 12px 30px; border: 2px solid var(--color-burgundy); border-radius: 5px; cursor: pointer;">
                <i class="fas fa-arrow-left" style="margin-right: 10px;"></i> Back
            </button>
            <button type="button" onclick="${step === totalSteps ? 'calculateResults()' : 'nextStep()'}" class="btn" style="background-color: var(--color-burgundy); color: var(--color-light); padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer;">
                ${step === totalSteps ? 'View Results' : 'Next'} ${step === totalSteps ? '' : '<i class="fas fa-arrow-right" style="margin-left: 10px;"></i>'}
            </button>
        </div>
    `;
    
    stepElement.innerHTML = content;
    return stepElement;
}

function calculateResults() {
    // This is a simplified calculation for demonstration purposes
    let score = 0;
    const strengths = [];
    const concerns = [];
    const recommendations = [];
    
    // Acceptance Letter
    if (formData.acceptanceLetter === "yes") {
        score += 20;
        strengths.push("You have a valid acceptance letter from a DLI");
    } else {
        score -= 20;
        concerns.push("You need an acceptance letter from a designated learning institution (DLI)");
        recommendations.push("Apply to a designated learning institution and obtain an acceptance letter");
    }
    
    // Financial Support
    if (formData.financialSupport === "sufficient") {
        score += 20;
        strengths.push("You have demonstrated sufficient financial support");
    } else if (formData.financialSupport === "partial") {
        score += 10;
        concerns.push("Your financial support may not be sufficient for your entire stay");
        recommendations.push("Secure additional financial support or provide more evidence of your financial capacity");
    } else {
        score -= 10;
        concerns.push("You need to demonstrate sufficient financial support for your studies and stay in Canada");
        recommendations.push("Secure financial support through personal funds, scholarships, or a combination");
    }
    
    // Calculate other factors...
    
    // Create results HTML
    const resultsHtml = `
        <div class="step" id="results">
            <div class="card" style="background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 30px;">
                <h2 style="color: var(--color-burgundy); margin-bottom: 20px;">Your Study Permit Assessment Results</h2>
                
                <div class="score-section" style="text-align: center; margin-bottom: 30px;">
                    <div class="score-circle" style="width: 150px; height: 150px; border-radius: 50%; background-color: ${score >= 30 ? 'var(--color-burgundy)' : '#dc3545'}; color: white; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <span style="font-size: 2.5rem; font-weight: bold;">${score}</span>
                        <span style="font-size: 0.9rem;">points</span>
                    </div>
                    <h3 style="color: ${score >= 30 ? 'var(--color-burgundy)' : '#dc3545'}; margin-bottom: 10px;">
                        ${score >= 30 ? 'Likely Eligible' : 'May Need Improvements'}
                    </h3>
                    <p style="color: var(--color-dark);">
                        ${score >= 30 ? 
                            'Based on your responses, you appear to meet the basic eligibility requirements for a study permit.' :
                            'Based on your responses, you may need to improve certain aspects of your application.'}
                    </p>
                </div>
                
                ${strengths.length > 0 ? `
                    <div class="strengths-section" style="margin-bottom: 20px;">
                        <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Strengths</h3>
                        <ul style="list-style-type: none; padding: 0;">
                            ${strengths.map(strength => `
                                <li style="margin-bottom: 10px; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle" style="color: var(--color-burgundy); margin-right: 10px; margin-top: 3px;"></i>
                                    <span>${strength}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                ` : ''}
                
                ${concerns.length > 0 ? `
                    <div class="concerns-section" style="margin-bottom: 20px;">
                        <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Areas of Concern</h3>
                        <ul style="list-style-type: none; padding: 0;">
                            ${concerns.map(concern => `
                                <li style="margin-bottom: 10px; display: flex; align-items: flex-start;">
                                    <i class="fas fa-exclamation-circle" style="color: #dc3545; margin-right: 10px; margin-top: 3px;"></i>
                                    <span>${concern}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                ` : ''}
                
                ${recommendations.length > 0 ? `
                    <div class="recommendations-section" style="margin-bottom: 20px;">
                        <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Recommendations</h3>
                        <ul style="list-style-type: none; padding: 0;">
                            ${recommendations.map(recommendation => `
                                <li style="margin-bottom: 10px; display: flex; align-items: flex-start;">
                                    <i class="fas fa-lightbulb" style="color: var(--color-burgundy); margin-right: 10px; margin-top: 3px;"></i>
                                    <span>${recommendation}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                ` : ''}
                
                <div class="next-steps" style="text-align: center; margin-top: 30px;">
                    <a href="../consultant.php" class="btn" style="background-color: var(--color-burgundy); color: var(--color-light); padding: 12px 30px; border: none; border-radius: 5px; text-decoration: none; display: inline-block;">
                        Book a Consultation
                    </a>
                </div>
            </div>
        </div>
    `;
    
    // Replace form with results
    document.getElementById('studyPermitForm').innerHTML = resultsHtml;
}

// Initialize first step
document.addEventListener('DOMContentLoaded', () => {
    updateProgress();
});
</script>

<?php include('../includes/footer.php'); ?>
