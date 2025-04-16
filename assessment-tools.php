<?php
$page_title = "Assessment Tools | CANEXT Immigration";
include('includes/header.php');
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/assessment-header.jpg'); background-size: cover; background-position: center; padding: 100px 0; color: var(--color-light); text-align: center;">
    <div class="container">
        <h1 data-aos="fade-up">Immigration Assessment Tools</h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb" style="display: flex; justify-content: center; list-style: none; padding: 0; margin: 20px 0 0 0;">
                <li class="breadcrumb-item"><a href="index.php" style="color: var(--color-cream);">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--color-light);">Assessment Tools</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Introduction Section -->
<section class="section intro-section" style="background-color: var(--color-cream);">
    <div class="container">
        <div class="section-content" style="text-align: center; max-width: 800px; margin: 0 auto;">
            <h2 class="section-title" data-aos="fade-up">Check Your Eligibility</h2>
            <p data-aos="fade-up" data-aos-delay="100">Our assessment tools help you evaluate your eligibility for various Canadian immigration programs. These tools provide preliminary assessments based on the information you provide, but please note they are for guidance only and not a guarantee of eligibility.</p>
            <p data-aos="fade-up" data-aos-delay="200">For a comprehensive evaluation, we recommend <a href="contact.php" style="color: var(--color-burgundy); font-weight: 600;">contacting our immigration experts</a> for personalized advice.</p>
        </div>
        
        <div class="tools-navigation" data-aos="fade-up" data-aos-delay="300" style="display: flex; justify-content: center; gap: 20px; margin-top: 40px; flex-wrap: wrap;">
            <a href="#eligibility" class="btn btn-primary">Visa Eligibility Calculator</a>
            <a href="#crs" class="btn btn-primary">CRS Score Calculator</a>
            <a href="#study" class="btn btn-primary">Study Permit Checker</a>
            <a href="#pathway" class="btn btn-primary">Immigration Pathway Calculator</a>
        </div>
    </div>
</section>

<!-- Visa Eligibility Calculator -->
<section id="eligibility" class="section calculator-section">
    <div class="container">
        <div class="section-header" style="text-align: center; margin-bottom: 50px;">
            <h2 class="section-title" data-aos="fade-up">Visa Eligibility Calculator</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Check if you meet the basic requirements for Canadian immigration programs</p>
        </div>
        
        <div class="calculator-container" style="max-width: 800px; margin: 0 auto; background-color: var(--color-light); padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);" data-aos="fade-up" data-aos-delay="200">
            <form id="eligibility-form" class="assessment-form">
                <div class="form-group">
                    <label for="age">Your Age</label>
                    <input type="number" id="age" name="age" min="18" max="65" required>
                </div>
                
                <div class="form-group">
                    <label for="education">Highest Level of Education</label>
                    <select id="education" name="education" required>
                        <option value="">Select education level</option>
                        <option value="high-school">High School</option>
                        <option value="diploma">Diploma / Certificate</option>
                        <option value="bachelors">Bachelor's Degree</option>
                        <option value="masters">Master's Degree</option>
                        <option value="phd">PhD</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="experience">Years of Work Experience</label>
                    <input type="number" id="experience" name="experience" min="0" max="30" required>
                </div>
                
                <div class="form-group">
                    <label for="language">English Language Proficiency</label>
                    <select id="language" name="language" required>
                        <option value="">Select proficiency level</option>
                        <option value="basic">Basic (CLB 4-5)</option>
                        <option value="intermediate">Intermediate (CLB 6-7)</option>
                        <option value="fluent">Fluent (CLB 8+)</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Calculate Eligibility</button>
            </form>
            
            <div id="eligibility-result" style="margin-top: 30px; padding: 20px; border-radius: 5px; display: none;">
                <!-- Results will be displayed here via JavaScript -->
            </div>
        </div>
    </div>
</section>

<!-- CRS Score Calculator -->
<section id="crs" class="section calculator-section" style="background-color: var(--color-cream);">
    <div class="container">
        <div class="section-header" style="text-align: center; margin-bottom: 50px;">
            <h2 class="section-title" data-aos="fade-up">CRS Score Calculator</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Calculate your Comprehensive Ranking System (CRS) score for Express Entry</p>
        </div>
        
        <div class="calculator-container" style="max-width: 800px; margin: 0 auto; background-color: var(--color-light); padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);" data-aos="fade-up" data-aos-delay="200">
            <form id="crs-form" class="assessment-form">
                <div class="form-group">
                    <label for="crs-age">Your Age</label>
                    <input type="number" id="crs-age" name="crs-age" min="18" max="65" required>
                </div>
                
                <div class="form-group">
                    <label for="crs-education">Highest Level of Education</label>
                    <select id="crs-education" name="crs-education" required>
                        <option value="">Select education level</option>
                        <option value="high-school">High School</option>
                        <option value="one-year">One-year degree/diploma</option>
                        <option value="two-year">Two-year degree/diploma</option>
                        <option value="bachelors">Bachelor's Degree</option>
                        <option value="masters">Master's Degree</option>
                        <option value="phd">PhD</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="crs-first-language">First Official Language (English/French)</label>
                    <select id="crs-first-language" name="crs-first-language" required>
                        <option value="">Select language level</option>
                        <option value="clb4">CLB 4</option>
                        <option value="clb5">CLB 5</option>
                        <option value="clb6">CLB 6</option>
                        <option value="clb7">CLB 7</option>
                        <option value="clb8">CLB 8</option>
                        <option value="clb9">CLB 9</option>
                        <option value="clb10">CLB 10+</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="crs-work-experience">Years of Work Experience</label>
                    <input type="number" id="crs-work-experience" name="crs-work-experience" min="0" max="30" required>
                </div>
                
                <div class="form-group">
                    <label for="crs-canadian-experience">Do you have Canadian work experience?</label>
                    <div style="display: flex; align-items: center;">
                        <input type="checkbox" id="crs-canadian-experience" name="crs-canadian-experience" style="margin-right: 10px;">
                        <label for="crs-canadian-experience" style="margin-bottom: 0;">Yes, I have worked in Canada</label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Calculate CRS Score</button>
            </form>
            
            <div id="crs-result" style="margin-top: 30px; padding: 20px; border-radius: 5px; display: none;">
                <!-- Results will be displayed here via JavaScript -->
            </div>
        </div>
    </div>
</section>

<!-- Study Permit Checker -->
<section id="study" class="section calculator-section">
    <div class="container">
        <div class="section-header" style="text-align: center; margin-bottom: 50px;">
            <h2 class="section-title" data-aos="fade-up">Study Permit Checker</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Check your eligibility for a Canadian study permit</p>
        </div>
        
        <div class="calculator-container" style="max-width: 800px; margin: 0 auto; background-color: var(--color-light); padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);" data-aos="fade-up" data-aos-delay="200">
            <form id="study-permit-form" class="assessment-form">
                <div class="form-group">
                    <label for="has-acceptance">Do you have an acceptance letter from a Canadian institution?</label>
                    <div style="display: flex; align-items: center;">
                        <input type="checkbox" id="has-acceptance" name="has-acceptance" style="margin-right: 10px;">
                        <label for="has-acceptance" style="margin-bottom: 0;">Yes, I have an acceptance letter</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="financial-support">Can you demonstrate financial support?</label>
                    <div style="display: flex; align-items: center;">
                        <input type="checkbox" id="financial-support" name="financial-support" style="margin-right: 10px;">
                        <label for="financial-support" style="margin-bottom: 0;">Yes, I can prove financial support</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="study-country">Country of Citizenship</label>
                    <select id="study-country" name="study-country" required>
                        <option value="">Select your country</option>
                        <option value="visa-free">Visa-exempt country</option>
                        <option value="visa-required">Visa-required country</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="study-duration">Intended Duration of Study</label>
                    <select id="study-duration" name="study-duration" required>
                        <option value="">Select duration</option>
                        <option value="less-6">Less than 6 months</option>
                        <option value="6-to-12">6-12 months</option>
                        <option value="more-12">More than 12 months</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Check Eligibility</button>
            </form>
            
            <div id="study-result" style="margin-top: 30px; padding: 20px; border-radius: 5px; display: none;">
                <!-- Results will be displayed here via JavaScript -->
            </div>
        </div>
    </div>
</section>

<!-- Immigration Pathway Calculator -->
<section id="pathway" class="section calculator-section" style="background-color: var(--color-cream);">
    <div class="container">
        <div class="section-header" style="text-align: center; margin-bottom: 50px;">
            <h2 class="section-title" data-aos="fade-up">Immigration Pathway Calculator</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Find your ideal pathway to Canada based on your specific circumstances</p>
        </div>
        
        <div class="calculator-container" style="max-width: 800px; margin: 0 auto; background-color: var(--color-light); padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);" data-aos="fade-up" data-aos-delay="200">
            <div class="tool-description" style="margin-bottom: 30px; text-align: center;">
                <p>Our Immigration Pathway Calculator helps you determine the most suitable immigration route for your specific situation. Whether your goal is to study, work, become a permanent resident, start a business, or simply visit Canada, this tool will guide you toward the right path.</p>
            </div>
            
            <div class="pathway-categories" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div class="pathway-card" style="border: 2px solid var(--color-burgundy); border-radius: 10px; padding: 20px; text-align: center;">
                    <i class="fas fa-graduation-cap" style="font-size: 2.5rem; color: var(--color-burgundy); margin-bottom: 15px;"></i>
                    <h3 style="margin-bottom: 10px;">Study in Canada</h3>
                    <p style="font-size: 0.9rem; margin-bottom: 15px;">Determine your eligibility for Canadian student permits and SDS streams</p>
                </div>
                
                <div class="pathway-card" style="border: 2px solid var(--color-burgundy); border-radius: 10px; padding: 20px; text-align: center;">
                    <i class="fas fa-briefcase" style="font-size: 2.5rem; color: var(--color-burgundy); margin-bottom: 15px;"></i>
                    <h3 style="margin-bottom: 10px;">Work in Canada</h3>
                    <p style="font-size: 0.9rem; margin-bottom: 15px;">Explore various work permit options including LMIA-based and open work permits</p>
                </div>
                
                <div class="pathway-card" style="border: 2px solid var(--color-burgundy); border-radius: 10px; padding: 20px; text-align: center;">
                    <i class="fas fa-home" style="font-size: 2.5rem; color: var(--color-burgundy); margin-bottom: 15px;"></i>
                    <h3 style="margin-bottom: 10px;">Permanent Residence</h3>
                    <p style="font-size: 0.9rem; margin-bottom: 15px;">Check pathways to permanent residency through Express Entry, PNP, and other programs</p>
                </div>
                
                <div class="pathway-card" style="border: 2px solid var(--color-burgundy); border-radius: 10px; padding: 20px; text-align: center;">
                    <i class="fas fa-plane" style="font-size: 2.5rem; color: var(--color-burgundy); margin-bottom: 15px;"></i>
                    <h3 style="margin-bottom: 10px;">Visit Canada</h3>
                    <p style="font-size: 0.9rem; margin-bottom: 15px;">Find information on visitor visas, eTAs, and travel requirements</p>
                </div>
            </div>
            
            <a href="assessment-calculator/immigration-pathway-calculator.php" class="btn btn-primary" style="width: 100%; display: block; text-align: center;">Start Pathway Calculator</a>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="section cta-section" style="background-image: linear-gradient(rgba(109, 35, 35, 0.9), rgba(109, 35, 35, 0.9)), url('images/cta-background.jpg'); background-size: cover; background-position: center; padding: 80px 0; color: var(--color-light); text-align: center;">
    <div class="container">
        <h2 data-aos="fade-up" style="font-size: 2.2rem; margin-bottom: 20px;">Need Professional Guidance?</h2>
        <p data-aos="fade-up" data-aos-delay="100" style="font-size: 1.1rem; margin-bottom: 30px; max-width: 700px; margin-left: auto; margin-right: auto;">Our online tools provide a preliminary assessment, but for a detailed evaluation of your specific situation, we recommend consulting with our immigration experts.</p>
        <a href="contact.php" class="btn btn-primary" data-aos="fade-up" data-aos-delay="200" style="background-color: var(--color-cream); color: var(--color-burgundy);">Contact an Expert</a>
    </div>
</section>

<?php include('includes/footer.php'); ?> 