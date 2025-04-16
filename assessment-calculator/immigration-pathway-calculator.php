<?php
$page_title = "Immigration Pathway Calculator | CANEXT Immigration";
include('../includes/header.php');
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../images/assessment/immigration-path-header.jpg'); background-size: cover; background-position: center; padding: 100px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
<div style="position: absolute; width: 300px; height: 300px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: -100px; left: -100px;"></div>
    <div style="position: absolute; width: 200px; height: 200px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); bottom: -50px; right: 10%; animation: pulse 4s infinite alternate;"></div>
    <div style="position: absolute; width: 100px; height: 100px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: 30%; right: 20%; animation: pulse 3s infinite alternate;"></div>
    <div class="container">
        <h1 data-aos="fade-up">Immigration Pathway Calculator</h1>
        <p data-aos="fade-up" data-aos-delay="100">Find your ideal pathway to Canada based on your specific circumstances</p>
    </div>
</section>

<!-- Define selectPathway function globally so it's available immediately -->
<script>
// Global variables and functions defined at the top of the document
var currentPathway = '';
var currentQuestion = 0;
var answers = {};

// This function must be defined globally BEFORE it's used in HTML
function selectPathway(pathway) {
    console.log("selectPathway called with pathway:", pathway);
    
    // Store the selected pathway
    currentPathway = pathway;
    currentQuestion = 0;
    answers = {};
    
    // Update UI - highlight selected pathway
    var cards = document.querySelectorAll('.pathway-card');
    for (var i = 0; i < cards.length; i++) {
        if (cards[i].getAttribute('data-path') === pathway) {
            cards[i].style.borderColor = 'var(--color-burgundy)';
            cards[i].style.backgroundColor = 'rgba(128, 0, 32, 0.05)';
        } else {
            cards[i].style.borderColor = '#f0f0f0';
            cards[i].style.backgroundColor = 'transparent';
        }
    }
    
    // Hide category selection and show questions
    document.getElementById('categorySelection').style.display = 'none';
    document.getElementById('questionsContainer').style.display = 'block';
    
    // Show the first question
    showQuestion();
}
</script>

<!-- Calculator Section -->
<section class="section calculator-section" style="padding: 60px 0;">
    <div class="container" style="max-width: 800px; margin: 0 auto;">
        <!-- Calculator Form -->
        <form id="pathwayCalculator" class="calculator-form" style="background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); overflow: hidden;">
            <!-- Category Selection Step -->
            <div id="categorySelection" style="padding: 30px;">
                <h2 style="color: var(--color-burgundy); margin-bottom: 20px;">Select Your Immigration Goal</h2>
                <p style="color: #666; margin-bottom: 30px;">Choose the option that best describes your primary goal for coming to Canada</p>
                
                <div class="pathway-options" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <!-- Using onclick with globally defined function -->
                    <div class="pathway-card" data-path="study" onclick="selectPathway('study')" style="border: 2px solid #f0f0f0; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-graduation-cap" style="font-size: 2.5rem; color: var(--color-burgundy); margin-bottom: 15px;"></i>
                        <h3 style="margin-bottom: 10px; color: var(--color-dark);">Study in Canada</h3>
                        <p style="color: #666; font-size: 0.9rem;">Obtain a student permit to pursue education in Canada</p>
                    </div>
                    
                    <div class="pathway-card" data-path="work" onclick="selectPathway('work')" style="border: 2px solid #f0f0f0; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-briefcase" style="font-size: 2.5rem; color: var(--color-burgundy); margin-bottom: 15px;"></i>
                        <h3 style="margin-bottom: 10px; color: var(--color-dark);">Work in Canada</h3>
                        <p style="color: #666; font-size: 0.9rem;">Get a work permit to advance your career in Canada</p>
                    </div>
                    
                    <div class="pathway-card" data-path="pr" onclick="selectPathway('pr')" style="border: 2px solid #f0f0f0; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-home" style="font-size: 2.5rem; color: var(--color-burgundy); margin-bottom: 15px;"></i>
                        <h3 style="margin-bottom: 10px; color: var(--color-dark);">Permanent Residence</h3>
                        <p style="color: #666; font-size: 0.9rem;">Become a permanent resident of Canada</p>
                    </div>
                    
                    <div class="pathway-card" data-path="business" onclick="selectPathway('business')" style="border: 2px solid #f0f0f0; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-chart-line" style="font-size: 2.5rem; color: var(--color-burgundy); margin-bottom: 15px;"></i>
                        <h3 style="margin-bottom: 10px; color: var(--color-dark);">Invest/Start Business</h3>
                        <p style="color: #666; font-size: 0.9rem;">Invest or establish a business in Canada</p>
                    </div>
                    
                    <div class="pathway-card" data-path="visitor" onclick="selectPathway('visitor')" style="border: 2px solid #f0f0f0; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-plane" style="font-size: 2.5rem; color: var(--color-burgundy); margin-bottom: 15px;"></i>
                        <h3 style="margin-bottom: 10px; color: var(--color-dark);">Visit Canada</h3>
                        <p style="color: #666; font-size: 0.9rem;">Apply for a Visitor Visa or eTA</p>
                    </div>
                </div>
            </div>
            
            <!-- Questions Container (Will be dynamically populated) -->
            <div id="questionsContainer" style="display: none; padding: 30px;">
                <!-- Question content will be loaded here -->
            </div>
            
            <!-- Results Container -->
            <div id="resultsContainer" style="display: none; padding: 30px;">
                <!-- Results will be shown here -->
            </div>
        </form>
    </div>
</section>

<script>
// Complete question data and functionality
var questions = {
    study: [
        {
            id: 'country',
            text: 'Which country are you a resident of?',
            type: 'select',
            options: [
                { value: 'india', label: 'India' },
                { value: 'philippines', label: 'Philippines' },
                { value: 'vietnam', label: 'Vietnam' },
                { value: 'morocco', label: 'Morocco' },
                { value: 'senegal', label: 'Senegal' },
                { value: 'other', label: 'Other country' }
            ]
        },
        {
            id: 'acceptance',
            text: 'Do you have a letter of acceptance from a Designated Learning Institution (DLI) in Canada?',
            type: 'radio',
            options: [
                { value: 'yes', label: 'Yes' },
                { value: 'no', label: 'No' }
            ]
        },
        {
            id: 'duration',
            text: 'Is your course duration valid (typically more than 6 months)?',
            type: 'radio',
            options: [
                { value: 'yes', label: 'Yes' },
                { value: 'no', label: 'No' }
            ]
        },
        {
            id: 'tuition',
            text: 'Have you paid your first year tuition?',
            type: 'radio',
            options: [
                { value: 'yes', label: 'Yes' },
                { value: 'no', label: 'No' }
            ]
        },
        {
            id: 'language',
            text: 'Do you have a valid language test result?',
            type: 'radio',
            options: [
                { value: 'yes', label: 'Yes' },
                { value: 'no', label: 'No' }
            ]
        },
        {
            id: 'gic',
            text: 'Do you have a Guaranteed Investment Certificate (GIC) of at least $10,000 CAD?',
            type: 'radio',
            options: [
                { value: 'yes', label: 'Yes' },
                { value: 'no', label: 'No' }
            ]
        },
        {
            id: 'medical',
            text: 'Have you completed a medical exam by an IRCC panel physician?',
            type: 'radio',
            options: [
                { value: 'yes', label: 'Yes' },
                { value: 'no', label: 'No' }
            ]
        }
    ],
    work: [
        {
            id: 'pr_application',
            text: 'Have you applied for Permanent Residence?',
            type: 'radio',
            options: [
                { value: 'yes', label: 'Yes' },
                { value: 'no', label: 'No' }
            ]
        },
        {
            id: 'youth_program',
            text: 'Are you eligible for a youth/international program (typically 18-35 years old)?',
            type: 'radio',
            options: [
                { value: 'yes', label: 'Yes' },
                { value: 'no', label: 'No' }
            ]
        },
        {
            id: 'job_offer',
            text: 'Do you have a job offer from a Canadian employer?',
            type: 'radio',
            options: [
                { value: 'yes', label: 'Yes' },
                { value: 'no', label: 'No' }
            ]
        }
    ],
    pr: [
        {
            id: 'express_entry',
            text: 'Are you interested in applying through Express Entry?',
            type: 'radio',
            options: [
                { value: 'yes', label: 'Yes' },
                { value: 'no', label: 'No' }
            ]
        },
        {
            id: 'selection_score',
            text: 'Do you have a selection factor score of 67 or higher?',
            type: 'radio',
            options: [
                { value: 'yes', label: 'Yes' },
                { value: 'no', label: 'No' },
                { value: 'unknown', label: 'I don\'t know' }
            ]
        },
        {
            id: 'occupation',
            text: 'Are you a self-employed artist, athlete, caregiver, or home support worker?',
            type: 'radio',
            options: [
                { value: 'artist', label: 'Self-employed artist or athlete' },
                { value: 'caregiver', label: 'Caregiver or home support worker' },
                { value: 'no', label: 'None of these' }
            ]
        },
        {
            id: 'entrepreneur',
            text: 'Are you interested in the entrepreneur immigration stream?',
            type: 'radio',
            options: [
                { value: 'yes', label: 'Yes' },
                { value: 'no', label: 'No' }
            ]
        }
    ],
    business: [
        {
            id: 'business_type',
            text: 'What type of business activity are you interested in?',
            type: 'radio',
            options: [
                { value: 'entrepreneur', label: 'Start a new business in Canada' },
                { value: 'investor', label: 'Invest in an existing business' },
                { value: 'self_employed', label: 'Self-employed professional' }
            ]
        },
        {
            id: 'investment',
            text: 'What is your planned investment amount?',
            type: 'select',
            options: [
                { value: 'under100k', label: 'Under $100,000 CAD' },
                { value: '100k-300k', label: '$100,000 - $300,000 CAD' },
                { value: '300k-500k', label: '$300,000 - $500,000 CAD' },
                { value: 'over500k', label: 'Over $500,000 CAD' }
            ]
        },
        {
            id: 'networth',
            text: 'What is your personal net worth?',
            type: 'select',
            options: [
                { value: 'under300k', label: 'Under $300,000 CAD' },
                { value: '300k-500k', label: '$300,000 - $500,000 CAD' },
                { value: '500k-1m', label: '$500,000 - $1 million CAD' },
                { value: 'over1m', label: 'Over $1 million CAD' }
            ]
        }
    ],
    visitor: [
        {
            id: 'purpose',
            text: 'What is the main purpose of your visit to Canada?',
            type: 'radio',
            options: [
                { value: 'tourism', label: 'Tourism or visiting family/friends' },
                { value: 'business', label: 'Business meetings or conferences' },
                { value: 'transit', label: 'Transit through Canada to another country' }
            ]
        },
        {
            id: 'nationality',
            text: 'What is your country of citizenship?',
            type: 'select',
            options: [
                { value: 'usa', label: 'United States' },
                { value: 'visa_exempt', label: 'Visa-exempt country (EU, UK, Australia, etc.)' },
                { value: 'visa_required', label: 'Visa-required country' }
            ]
        },
        {
            id: 'us_status',
            text: 'Are you a lawful permanent resident of the USA?',
            type: 'radio',
            options: [
                { value: 'yes', label: 'Yes' },
                { value: 'no', label: 'No' }
            ]
        }
    ]
};

// Function to show the current question
function showQuestion() {
    var container = document.getElementById('questionsContainer');
    var question = questions[currentPathway][currentQuestion];
    
    if (!question) {
        showResults();
        return;
    }
    
    var optionsHTML = '';
    if (question.type === 'radio') {
        question.options.forEach(function(option) {
            optionsHTML += '<label style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; cursor: pointer;">' +
                '<input type="radio" name="' + question.id + '" value="' + option.value + '" ' + 
                (answers[question.id] === option.value ? 'checked' : '') + '>' +
                '<span>' + option.label + '</span>' +
                '</label>';
        });
    } else if (question.type === 'select') {
        optionsHTML = '<select name="' + question.id + '" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">' +
            '<option value="">Please select</option>';
        
        question.options.forEach(function(option) {
            optionsHTML += '<option value="' + option.value + '" ' + 
                (answers[question.id] === option.value ? 'selected' : '') + '>' + 
                option.label + '</option>';
        });
        
        optionsHTML += '</select>';
    }
    
    container.innerHTML = '<h2 style="color: var(--color-burgundy); margin-bottom: 20px;">Question ' + 
        (currentQuestion + 1) + ' of ' + questions[currentPathway].length + '</h2>' +
        '<div class="progress" style="height: 8px; background-color: #e9ecef; border-radius: 4px; margin-bottom: 20px;">' +
        '<div class="progress-bar" style="width: ' + ((currentQuestion / questions[currentPathway].length) * 100) + 
        '%; height: 100%; border-radius: 4px; background-color: var(--color-burgundy);"></div>' +
        '</div>' +
        '<div class="question-container" style="margin-bottom: 30px;">' +
        '<h3 style="margin-bottom: 15px; color: var(--color-dark);">' + question.text + '</h3>' +
        '<div class="options-container" style="margin-top: 15px;">' + optionsHTML + '</div>' +
        '</div>' +
        '<div class="navigation" style="display: flex; justify-content: space-between;">' +
        '<button type="button" onclick="previousQuestion()" class="btn" ' +
        'style="background-color: white; color: var(--color-burgundy); padding: 12px 30px; border: 1px solid var(--color-burgundy); border-radius: 5px; cursor: pointer; ' + 
        (currentQuestion === 0 ? 'visibility: hidden;' : '') + '">' +
        '<i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back</button>' +
        '<button type="button" onclick="nextQuestion()" class="btn" ' +
        'style="background-color: var(--color-burgundy); color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer;">' +
        (currentQuestion === questions[currentPathway].length - 1 ? 'View Results' : 'Next') + 
        ' <i class="fas fa-arrow-right" style="margin-left: 8px;"></i></button>' +
        '</div>';
    
    // Add event listeners to the form elements
    if (question.type === 'radio') {
        var radios = document.querySelectorAll('input[name="' + question.id + '"]');
        for (var i = 0; i < radios.length; i++) {
            radios[i].addEventListener('change', function() {
                answers[question.id] = this.value;
            });
        }
    } else if (question.type === 'select') {
        var select = document.querySelector('select[name="' + question.id + '"]');
        if (select) {
            select.addEventListener('change', function() {
                answers[question.id] = this.value;
            });
        }
    }
}

// Function to go to the next question
function nextQuestion() {
    var question = questions[currentPathway][currentQuestion];
    var value;
    
    if (question.type === 'radio') {
        var checkedRadio = document.querySelector('input[name="' + question.id + '"]:checked');
        value = checkedRadio ? checkedRadio.value : null;
    } else if (question.type === 'select') {
        var select = document.querySelector('select[name="' + question.id + '"]');
        value = select ? select.value : null;
    }
    
    if (!value) {
        alert('Please select an answer to continue');
        return;
    }
    
    answers[question.id] = value;
    currentQuestion++;
    
    if (currentQuestion < questions[currentPathway].length) {
        showQuestion();
    } else {
        showResults();
    }
}

// Function to go to the previous question
function previousQuestion() {
    if (currentQuestion > 0) {
        currentQuestion--;
        showQuestion();
    }
}

// Function to show results
function showResults() {
    var container = document.getElementById('questionsContainer');
    container.style.display = 'none';
    
    var resultsContainer = document.getElementById('resultsContainer');
    resultsContainer.style.display = 'block';
    
    // Evaluate the pathway based on answers
    var result = evaluatePathway();
    
    resultsContainer.innerHTML = '<h2 style="color: var(--color-burgundy); margin-bottom: 20px;">Your Immigration Pathway Results</h2>' +
        '<div class="result-box" style="background-color: ' + (result.eligible ? '#f0f7e6' : '#fff1f0') + '; border-radius: 8px; padding: 20px; margin-bottom: 30px;">' +
        '<div style="display: flex; align-items: center; margin-bottom: 15px;">' +
        '<i class="fas ' + (result.eligible ? 'fa-check-circle' : 'fa-exclamation-circle') + '" style="font-size: 2rem; color: ' + 
        (result.eligible ? '#5cb85c' : '#d9534f') + '; margin-right: 15px;"></i>' +
        '<h3 style="color: ' + (result.eligible ? '#5cb85c' : '#d9534f') + '; margin: 0;">' + result.title + '</h3>' +
        '</div>' +
        '<p style="margin-bottom: 10px;">' + result.message + '</p>' +
        '</div>' +
        '<div class="recommendation-box" style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 30px;">' +
        '<h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Next Steps</h3>' +
        '<ul style="list-style: none; padding: 0;">';
    
    // Add each next step to the results
    result.nextSteps.forEach(function(step) {
        resultsContainer.innerHTML += '<li style="display: flex; align-items: start; gap: 10px; margin-bottom: 15px;">' +
            '<i class="fas fa-arrow-right" style="color: var(--color-burgundy); margin-top: 5px;"></i>' +
            '<span>' + step + '</span>' +
            '</li>';
    });
    
    resultsContainer.innerHTML += '</ul></div>' +
        '<div class="action-buttons" style="display: flex; justify-content: center; gap: 20px;">' +
        '<button onclick="resetCalculator()" class="btn" ' +
        'style="background-color: white; color: var(--color-burgundy); padding: 12px 30px; border: 1px solid var(--color-burgundy); border-radius: 5px; cursor: pointer;">' +
        'Start Over</button>' +
        '<a href="../consultant.php" class="btn" ' +
        'style="background-color: var(--color-burgundy); color: white; padding: 12px 30px; border: none; border-radius: 5px; text-decoration: none; display: inline-block;">' +
        'Book a Consultation</a>' +
        '</div>';
}

function evaluatePathway() {
    switch(currentPathway) {
        case 'study':
            return evaluateStudyPathway();
        case 'work':
            return evaluateWorkPathway();
        case 'pr':
            return evaluatePRPathway();
        case 'business':
            return evaluateBusinessPathway();
        case 'visitor':
            return evaluateVisitorPathway();
        default:
            return {
                eligible: false,
                title: 'Unable to evaluate',
                message: 'Please try again or contact a consultant for personalized assistance.',
                nextSteps: ['Contact a CANEXT immigration consultant for personalized guidance.']
            };
    }
}

function evaluateStudyPathway() {
    // For SDS stream eligibility
    var sdsCountries = ['india', 'philippines', 'vietnam', 'morocco', 'senegal'];
    var isSdsEligible = sdsCountries.includes(answers.country) && 
                          answers.acceptance === 'yes' && 
                          answers.duration === 'yes' && 
                          answers.tuition === 'yes' && 
                          answers.language === 'yes' && 
                          answers.gic === 'yes' && 
                          answers.medical === 'yes';
    
    // Regular study permit eligibility with less strict requirements
    var isRegularEligible = answers.acceptance === 'yes' && 
                             answers.duration === 'yes';
    
    if (isSdsEligible) {
        return {
            eligible: true,
            title: 'Eligible for Student Direct Stream (SDS)',
            message: 'Based on your answers, you appear to be eligible to apply under the Student Direct Stream (SDS), which offers faster processing times.',
            nextSteps: [
                'Ensure your language test results meet the minimum requirements (usually IELTS 6.0 in each skill).',
                'Prepare and submit your application through the IRCC online portal.',
                'Consider booking a consultation with CANEXT Immigration for application review.',
                'Begin preparing for your journey to Canada once your application is approved.'
            ]
        };
    } else if (isRegularEligible) {
        return {
            eligible: true,
            title: 'Potentially Eligible for Regular Study Permit',
            message: 'You appear to be eligible for a regular study permit application. However, you may need to address some requirements to strengthen your application.',
            nextSteps: [
                'Ensure you have proof of financial support for your studies.',
                'Obtain a valid language test result if required by your educational institution.',
                'Complete a medical examination with an IRCC panel physician.',
                'Book a consultation with CANEXT Immigration for personalized guidance.'
            ]
        };
    } else {
        var missingRequirements = [];
        
        if (answers.acceptance !== 'yes') missingRequirements.push('Letter of acceptance from a Designated Learning Institution (DLI)');
        if (answers.duration !== 'yes') missingRequirements.push('Valid course duration (typically more than 6 months)');
        if (sdsCountries.includes(answers.country) && answers.tuition !== 'yes') missingRequirements.push('Payment of first year tuition');
        if (sdsCountries.includes(answers.country) && answers.language !== 'yes') missingRequirements.push('Valid language test results');
        if (sdsCountries.includes(answers.country) && answers.gic !== 'yes') missingRequirements.push('Guaranteed Investment Certificate (GIC)');
        if (sdsCountries.includes(answers.country) && answers.medical !== 'yes') missingRequirements.push('Medical examination');
        
        return {
            eligible: false,
            title: 'Requirements Not Yet Met',
            message: 'Based on your answers, you do not currently meet all requirements for a study permit. Missing requirements:',
            nextSteps: missingRequirements.map(function(req) { return 'Obtain/complete: ' + req; }).concat([
                'Book a consultation with CANEXT Immigration to develop a personalized plan.'
            ])
        };
    }
}

function evaluateWorkPathway() {
    if (answers.pr_application === 'yes') {
        return {
            eligible: true,
            title: 'Potentially Eligible for a Bridging Open Work Permit',
            message: 'If you have applied for Permanent Residence, you may be eligible for a Bridging Open Work Permit while your PR application is in process.',
            nextSteps: [
                'Check if your PR application has been acknowledged by IRCC.',
                'Ensure your current work permit will expire within 4 months.',
                'Apply for a Bridging Open Work Permit through the IRCC online portal.',
                'Book a consultation with CANEXT Immigration for assistance with your application.'
            ]
        };
    } else if (answers.youth_program === 'yes') {
        return {
            eligible: true,
            title: 'Potentially Eligible for International Experience Canada (IEC)',
            message: 'Based on your age, you may be eligible for the International Experience Canada program, which includes Working Holiday, Young Professionals, and International Co-op programs.',
            nextSteps: [
                'Check if your country has an IEC agreement with Canada.',
                'Create an online profile in the IEC pool.',
                'Wait for an invitation to apply if eligible.',
                'Book a consultation with CANEXT Immigration to determine the best IEC category for you.'
            ]
        };
    } else if (answers.job_offer === 'yes') {
        return {
            eligible: true,
            title: 'Potentially Eligible for Employer-Specific Work Permit',
            message: 'With a job offer from a Canadian employer, you may be eligible for an employer-specific work permit.',
            nextSteps: [
                'Verify if your job offer requires a Labour Market Impact Assessment (LMIA).',
                'Ensure your employer has completed all necessary steps.',
                'Gather all required documents for your work permit application.',
                'Book a consultation with CANEXT Immigration for guidance on the application process.'
            ]
        };
    } else {
        return {
            eligible: false,
            title: 'Additional Information Required',
            message: 'Based on your answers, we need more information to determine your eligibility for a work permit.',
            nextSteps: [
                'Explore other work permit categories such as intra-company transfers.',
                'Consider participating in the Global Talent Stream if you have specialized skills.',
                'Look into CUSMA/NAFTA professional categories if you're a citizen of USA or Mexico.',
                'Book a consultation with CANEXT Immigration for personalized pathways.'
            ]
        };
    }
}

function evaluatePRPathway() {
    // PR pathway evaluation code remains the same
    if (answers.express_entry === 'yes' && answers.selection_score === 'yes') {
        return {
            eligible: true,
            title: 'Potentially Eligible for Express Entry',
            message: 'With a selection factor score of 67 or higher, you may be eligible to enter the Express Entry pool.',
            nextSteps: [
                'Create an Express Entry profile on the IRCC website.',
                'Obtain a valid language test result (IELTS or CELPIP for English).',
                'Get an Educational Credential Assessment (ECA) if your education was outside Canada.',
                'Book a consultation with CANEXT Immigration to maximize your CRS score.'
            ]
        };
    } else if (answers.occupation === 'artist') {
        return {
            eligible: true,
            title: 'Potentially Eligible under Self-Employed Program',
            message: 'As a self-employed artist or athlete, you may be eligible under the Self-Employed Persons Program.',
            nextSteps: [
                'Gather evidence of your experience and international recognition.',
                'Prepare a business plan for your activities in Canada.',
                'Book a consultation with CANEXT Immigration to guide you through the application process.'
            ]
        };
    } else {
        return {
            eligible: false,
            title: 'Additional Pathways May Be Available',
            message: 'Based on your answers, we recommend exploring other permanent residence pathways.',
            nextSteps: [
                'Consider provincial nominee programs based on your skills and experience.',
                'Explore the Atlantic Immigration Program if interested in Atlantic provinces.',
                'Look into Rural and Northern Immigration Pilot if open to smaller communities.',
                'Book a consultation with CANEXT Immigration for personalized guidance.'
            ]
        };
    }
}

function evaluateBusinessPathway() {
    // Simplified business pathway evaluation
    return {
        eligible: true,
        title: 'Potentially Eligible for Business Immigration',
        message: 'Based on your answers, you may qualify for business immigration streams.',
        nextSteps: [
            'Research provincial business immigration programs that align with your business goals.',
            'Prepare a detailed business plan for your proposed venture in Canada.',
            'Gather proof of your net worth and business experience.',
            'Book a consultation with CANEXT Immigration for guidance on business immigration strategies.'
        ]
    };
}

function evaluateVisitorPathway() {
    // Simplified visitor pathway evaluation
    if (answers.nationality === 'usa' || answers.nationality === 'visa_exempt') {
        return {
            eligible: true,
            title: 'Eligible for eTA or Visa-Exempt Entry',
            message: 'As a citizen of a visa-exempt country, you may enter Canada with just a passport or eTA.',
            nextSteps: [
                'Apply for an eTA online before your trip if required.',
                'Prepare documentation showing ties to your home country and purpose of visit.',
                'Ensure you have sufficient funds for your stay.',
                'Consider booking a consultation with CANEXT Immigration if you have admissibility concerns.'
            ]
        };
    } else {
        return {
            eligible: true,
            title: 'Visitor Visa (TRV) Required',
            message: 'Based on your citizenship, you will need to apply for a Temporary Resident Visa (visitor visa) to enter Canada.',
            nextSteps: [
                'Gather documents showing ties to your home country.',
                'Prepare proof of funds for your visit.',
                'Have a clear travel itinerary and purpose of visit.',
                'Book a consultation with CANEXT Immigration for assistance with your visitor visa application.'
            ]
        };
    }
}

// Function to reset the calculator
function resetCalculator() {
    // Reset state
    currentPathway = '';
    currentQuestion = 0;
    answers = {};
    
    // Reset UI
    document.getElementById('resultsContainer').style.display = 'none';
    document.getElementById('questionsContainer').style.display = 'none';
    document.getElementById('categorySelection').style.display = 'block';
    
    // Reset pathway selection styling
    var cards = document.querySelectorAll('.pathway-card');
    for (var i = 0; i < cards.length; i++) {
        cards[i].style.borderColor = '#f0f0f0';
        cards[i].style.backgroundColor = 'transparent';
    }
}

// Add hover effects
document.addEventListener('DOMContentLoaded', function() {
    var cards = document.querySelectorAll('.pathway-card');
    
    for (var i = 0; i < cards.length; i++) {
        cards[i].addEventListener('mouseenter', function() {
            if (this.getAttribute('data-path') !== currentPathway) {
                this.style.borderColor = 'var(--color-burgundy)';
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 10px 15px rgba(0,0,0,0.1)';
            }
        });
        
        cards[i].addEventListener('mouseleave', function() {
            if (this.getAttribute('data-path') !== currentPathway) {
                this.style.borderColor = '#f0f0f0';
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            }
        });
    }
});
</script>

<?php include('../includes/footer.php'); ?> 