<?php
$page_title = "CANEXT | Canadian Immigration Consultancy";
include('includes/header.php');
?>

<!-- All your existing index.php content goes here -->
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-content">
                <h1 class="hero-title">Immigration Simplified For
                    <div class="animated-text-wrapper"></div>
                </h1>
                <p class="hero-subtitle">Your trusted partner for Canadian immigration services</p>
                <div class="hero-buttons">
                    <a href="assessment-tools.php" class="btn btn-primary">Check Eligibility</a>
                    <a href="contact.php" class="btn btn-secondary">Get Consultation</a>
                </div>
            </div>
            <div class="hero-image-container">
                <div class="floating-image">
                    <img src="images/plane.png" alt="Immigration Services">
                </div>
                <div class="decoration-circle circle-1"></div>
                <div class="decoration-circle circle-2"></div>
                <div class="decoration-dot dot-1"></div>
                <div class="decoration-dot dot-2"></div>
                <div class="decoration-dot dot-3"></div>
            </div>
        </div>
    </div>
</section>

<style>
.hero {
    padding: 80px 0;
    background-color: #042167;
    color: var(--color-light);
    overflow: hidden;
    position: relative;
}

.hero-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    align-items: center;
    gap: 50px;
}

.hero-content {
    text-align: left;
    max-width: 600px;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    line-height: 1.2;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

.animated-text-wrapper {
    height: 60px;
    overflow: hidden;
    position: relative;
    margin: 10px 0;
}

.animated-text {
    display: block;
    color: #eaaa34;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    position: absolute;
    width: 100%;
    font-weight: 700;
    font-size: 3.5rem;
    transform: translateY(100%);
    opacity: 0;
    transition: transform 0.5s ease-out, opacity 0.5s ease-out;
}

.animated-text.current {
    transform: translateY(0);
    opacity: 1;
}

.animated-text.exiting {
    transform: translateY(-100%);
    opacity: 0;
}

.animated-text.next {
    transform: translateY(100%);
    opacity: 0;
}

@keyframes slideUp {
    0% {
        transform: translateY(100%);
        opacity: 0;
    }
    10% {
        transform: translateY(0);
        opacity: 1;
    }
    90% {
        transform: translateY(0);
        opacity: 1;
    }
    100% {
        transform: translateY(-100%);
        opacity: 0;
    }
}

.hero-subtitle {
    font-size: 1.2rem;
    margin-bottom: 30px;
    line-height: 1.6;
    opacity: 0.9;
}

.hero-buttons {
    display: flex;
    gap: 20px;
}

.hero-image-container {
    position: relative;
    height: 500px;
}

.floating-image {
    position: relative;
    animation: float 6s ease-in-out infinite;
}

.floating-image img {
    max-width: 100%;
    height: auto;
}

.decoration-circle {
    position: absolute;
    border-radius: 50%;
    border: 2px solid rgba(234, 170, 52, 0.2);
}

.circle-1 {
    width: 200px;
    height: 200px;
    top: 20%;
    right: 10%;
    animation: float 8s ease-in-out infinite;
}

.circle-2 {
    width: 100px;
    height: 100px;
    bottom: 30%;
    left: 10%;
    animation: float 6s ease-in-out infinite;
}

.decoration-dot {
    position: absolute;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #eaaa34;
}

.dot-1 {
    top: 20%;
    left: 20%;
    animation: float 4s ease-in-out infinite;
}

.dot-2 {
    top: 50%;
    right: 15%;
    animation: float 5s ease-in-out infinite;
}

.dot-3 {
    bottom: 30%;
    right: 30%;
    animation: float 7s ease-in-out infinite;
}

@keyframes float {
    0% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
    100% {
        transform: translateY(0);
    }
}

@media (max-width: 992px) {
    .hero-grid {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .hero-content {
        text-align: center;
        margin: 0 auto;
    }

    .hero-buttons {
        justify-content: center;
    }

    .hero-image-container {
        height: 400px;
        order: -1;
    }

    .hero-title {
        font-size: 2.5rem;
    }
}

@media (max-width: 768px) {
    .hero {
        padding: 60px 0;
    }

    .hero-image-container {
        height: 300px;
    }

    .hero-buttons {
        flex-direction: column;
        gap: 15px;
    }

    .hero-buttons .btn {
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
    }
}

.btn-primary {
    background-color: #eaaa34;
    color: #fff;
    border: 2px solid #eaaa34;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: transparent;
    color: #eaaa34;
}

.btn-secondary {
    background-color: transparent;
    color: #eaaa34;
    border: 2px solid #eaaa34;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background-color: #eaaa34;
    color: #fff;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const visaTypes = [
        'Study Permits',
        'Work Permits',
        'Express Entry',
        'Provincial Nominee',
        'Family Sponsorship',
        'Super Visa',
        'Visitor Visa'
    ];
    
    const wrapper = document.querySelector('.animated-text-wrapper');
    let currentIndex = 0;
    let nextIndex = 1;
    
    // Create text elements
    const currentText = document.createElement('div');
    const nextText = document.createElement('div');
    currentText.className = 'animated-text current';
    nextText.className = 'animated-text next';
    wrapper.appendChild(currentText);
    wrapper.appendChild(nextText);
    
    function updateText() {
        // Set text content
        currentText.textContent = visaTypes[currentIndex];
        nextText.textContent = visaTypes[nextIndex];
        
        // Start animation
        currentText.classList.add('exiting');
        currentText.classList.remove('current');
        
        nextText.classList.add('current');
        nextText.classList.remove('next');
        
        // After animation completes
        setTimeout(() => {
            // Reset the exiting text for next animation
            currentText.classList.remove('exiting');
            currentText.classList.add('next');
            
            // Update indices
            currentIndex = nextIndex;
            nextIndex = (nextIndex + 1) % visaTypes.length;
            
            // Prepare elements for next animation
            currentText.style.transition = 'none';
            currentText.style.transform = 'translateY(100%)';
            
            // Force reflow
            currentText.offsetHeight;
            
            // Re-enable transitions
            currentText.style.transition = '';
            
            // Swap elements
            [currentText, nextText] = [nextText, currentText];
        }, 500);
    }
    
    // Initial text setup
    currentText.textContent = visaTypes[0];
    nextText.textContent = visaTypes[1];
    
    // Start the animation loop
    setInterval(updateText, 3000);
});
</script>

<!-- Why Choose Us Section -->
<section class="section why-us" style="background-color: #042167;">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Why Choose CANEXT</h2>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Here's what sets us apart from other immigration consultancies</p>
        
        <div class="features-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            <!-- Feature 1 -->
            <div class="feature-card" data-aos="fade-up" data-aos-delay="200" style="text-align: center; padding: 30px; background-color: #fff; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <div class="feature-icon" style="font-size: 3rem; color: #eaaa34; margin-bottom: 20px;">
                    <i class="fas fa-award"></i>
                </div>
                <h3 style="color: #042167; margin-bottom: 15px;">Licensed Consultants</h3>
                <p>Our team consists of ICCRC licensed consultants with extensive experience in Canadian immigration.</p>
            </div>
            
            <!-- Feature 2 -->
            <div class="feature-card" data-aos="fade-up" data-aos-delay="300" style="text-align: center; padding: 30px; background-color: #fff; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <div class="feature-icon" style="font-size: 3rem; color: #eaaa34; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 style="color: #042167; margin-bottom: 15px;">High Success Rate</h3>
                <p>We pride ourselves on our high application success rate through meticulous preparation and attention to detail.</p>
            </div>
            
            <!-- Feature 3 -->
            <div class="feature-card" data-aos="fade-up" data-aos-delay="400" style="text-align: center; padding: 30px; background-color: #fff; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <div class="feature-icon" style="font-size: 3rem; color: #eaaa34; margin-bottom: 20px;">
                    <i class="fas fa-user-friends"></i>
                </div>
                <h3 style="color: #042167; margin-bottom: 15px;">Personalized Approach</h3>
                <p>We develop customized immigration strategies tailored to your unique situation and goals.</p>
            </div>
            
            <!-- Feature 4 -->
            <div class="feature-card" data-aos="fade-up" data-aos-delay="500" style="text-align: center; padding: 30px; background-color: #fff; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <div class="feature-icon" style="font-size: 3rem; color: #eaaa34; margin-bottom: 20px;">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 style="color: #042167; margin-bottom: 15px;">End-to-End Support</h3>
                <p>From initial assessment to settlement guidance, we support you throughout your entire immigration journey.</p>
            </div>
        </div>
    </div>
</section>

<!-- Assessment Tools Section -->
<section class="section assessment-tools" id="assessment-tools" style="background-color: #fff;">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Immigration Assessment Tools</h2>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Check your eligibility and prepare for your Canadian immigration journey</p>
        
        <div class="tools-grid">
            <!-- Visa Eligibility Tool -->
            <div class="tool-card" data-aos="fade-up" data-aos-delay="200">
                <div class="tool-icon">
                    <i class="fas fa-passport"></i>
                </div>
                <h3 class="tool-title">Visa Eligibility Calculator</h3>
                <p>Quickly determine if you qualify for various Canadian immigration programs based on your profile.</p>
                <a href="assessment-calculator/eligibility-calculator.php" class="btn btn-primary" style="margin-top: 20px;">Check Eligibility</a>
            </div>
            
            <!-- CRS Calculator -->
            <div class="tool-card" data-aos="fade-up" data-aos-delay="300">
                <div class="tool-icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <h3 class="tool-title">CRS Score Calculator</h3>
                <p>Calculate your Comprehensive Ranking System score for Express Entry applications.</p>
                <a href="assessment-calculator/crs-score-calculator.php" class="btn btn-primary" style="margin-top: 20px;">Calculate Score</a>
            </div>
            
            <!-- Study Permit Checker -->
            <div class="tool-card" data-aos="fade-up" data-aos-delay="400">
                <div class="tool-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3 class="tool-title">Study Permit Checker</h3>
                <p>Verify if you meet the requirements for a Canadian study permit and what documents you'll need.</p>
                <a href="assessment-calculator/study-permit-checker.php" class="btn btn-primary" style="margin-top: 20px;">Check Requirements</a>
            </div>
        </div>
    </div>
</section>

<!-- Success Stories Section -->
<section class="section success-stories" id="success-stories" style="background-color: #042167; padding: 80px 0;">
    <div class="container">
        <h2 class="section-title" style="font-size: 2.5rem; color: #fff; text-align: center; margin-bottom: 10px;">Check what's our client</h2>
        <h2 class="section-title" style="font-size: 2.5rem; color: #fff; text-align: center; margin-bottom: 60px;">say about us!</h2>
        
        <div class="testimonials-container" style="display: flex; gap: 25px; justify-content: center; flex-wrap: wrap; position: relative;">
            <!-- Navigation buttons -->
            <button class="nav-btn prev" style="position: absolute; left: -20px; top: 50%; transform: translateY(-50%); width: 50px; height: 50px; background: #eaaa34; border-radius: 50%; color: white; border: none; cursor: pointer; z-index: 10; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <!-- Testimonial 1 -->
            <div class="testimonial-card" style="background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); position: relative; max-width: 350px; flex: 1;">
                <div style="color: #042167; margin-bottom: 15px; font-weight: 500;">Express Entry Program</div>
                <p class="story-content" style="font-size: 1.1em; line-height: 1.6; margin-bottom: 20px; position: relative;">
                    "CANEXT made my Express Entry application process smooth and stress-free. Their guidance helped me secure permanent residency in just 6 months. Their attention to detail and expertise made all the difference!"
                </p>
                <div style="display: flex; align-items: center; margin-top: 30px;">
                    <div>
                        <h4 style="color: #042167; margin: 0; font-weight: 600;">John Smith</h4>
                        <p style="margin: 5px 0 0 0; color: #666;">Software Engineer, UK</p>
                    </div>
                </div>
                <div style="position: absolute; top: 20px; right: 20px; font-size: 3.5em; color: #042167; opacity: 0.2; font-family: serif;">"</div>
            </div>
            
            <!-- Testimonial 2 -->
            <div class="testimonial-card" style="background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); position: relative; max-width: 350px; flex: 1;">
                <div style="color: #042167; margin-bottom: 15px; font-weight: 500;">Study Permit</div>
                <p class="story-content" style="font-size: 1.1em; line-height: 1.6; margin-bottom: 20px; position: relative;">
                    "Thanks to CANEXT, I was accepted into my dream university in Canada. They helped me with my study permit application and gave me valuable advice on preparing for my new life as an international student."
                </p>
                <div style="display: flex; align-items: center; margin-top: 30px;">
                    <div>
                        <h4 style="color: #042167; margin: 0; font-weight: 600;">Maria Garcia</h4>
                        <p style="margin: 5px 0 0 0; color: #666;">Student, Mexico</p>
                    </div>
                </div>
                <div style="position: absolute; top: 20px; right: 20px; font-size: 3.5em; color: #042167; opacity: 0.2; font-family: serif;">"</div>
            </div>
            
            <!-- Testimonial 3 -->
            <div class="testimonial-card" style="background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); position: relative; max-width: 350px; flex: 1;">
                <div style="color: #042167; margin-bottom: 15px; font-weight: 500;">Work Permit</div>
                <p class="story-content" style="font-size: 1.1em; line-height: 1.6; margin-bottom: 20px; position: relative;">
                    "After struggling to secure a Canadian work permit on my own, I turned to CANEXT. Their expertise and strategic approach helped me obtain a work permit within weeks. I'm now working for a top company in Toronto!"
                </p>
                <div style="display: flex; align-items: center; margin-top: 30px;">
                    <div>
                        <h4 style="color: #042167; margin: 0; font-weight: 600;">Ahmed Hassan</h4>
                        <p style="margin: 5px 0 0 0; color: #666;">IT Specialist, Egypt</p>
                    </div>
                </div>
                <div style="position: absolute; top: 20px; right: 20px; font-size: 3.5em; color: #042167; opacity: 0.2; font-family: serif;">"</div>
            </div>
            
            <button class="nav-btn next" style="position: absolute; right: -20px; top: 50%; transform: translateY(-50%); width: 50px; height: 50px; background: #eaaa34; border-radius: 50%; color: white; border: none; cursor: pointer; z-index: 10; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        
  
    </div>
</section>


<!-- Call to Action Section -->
<section class="section cta" style="background-image: linear-gradient(rgba(4, 33, 103, 0.95), rgba(4, 33, 103, 0.95)), url('images/cta.png'); background-size: cover; background-position: center; padding: 100px 0; color: var(--color-light); text-align: center;">
    <div class="container">
        <h2 data-aos="fade-up" style="font-size: 2.5rem; margin-bottom: 20px;">Start Your Canadian Journey Today</h2>
        <p data-aos="fade-up" data-aos-delay="100" style="font-size: 1.2rem; margin-bottom: 30px; max-width: 700px; margin-left: auto; margin-right: auto;">Let our experts help you navigate the complex immigration process and achieve your Canadian dreams with confidence.</p>
        <div data-aos="fade-up" data-aos-delay="200">
            <a href="contact.php" class="btn btn-primary" style="background-color: #eaaa34; color: #fff; margin-right: 15px;">Contact Us</a>
            <a href="assessment-tools.php" class="btn btn-secondary" style="border-color: #eaaa34; color: #eaaa34;">Check Eligibility</a>
        </div>
    </div>
</section>

<!-- Contact Section Preview -->
<section class="section contact" id="contact">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Get In Touch</h2>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Have questions about Canadian immigration? Contact us for expert advice.</p>
        
        <div class="contact-grid">
            <div class="contact-info" data-aos="fade-up" data-aos-delay="200">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Our Office</h4>
                        <p>2233 Argentina Rd, Mississauga ON L5N 2X7, Canada</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Phone</h4>
                        <p>+1 (647) 226-7436</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Email</h4>
                        <p>info@canext.com</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Office Hours</h4>
                        <p>Monday to Friday: 9am - 5pm</p>
                        <p>Saturday: 10am - 2pm (By appointment only)</p>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-container" data-aos="fade-up" data-aos-delay="300">
                <form id="contact-form" class="contact-form" action="php/process_contact.php" method="POST">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    
                    <div class="form-group">
                        <label for="service">Service of Interest</label>
                        <select id="service" name="service">
                            <option value="">Select a service</option>
                            <option value="study">Study Permit</option>
                            <option value="work">Work Permit</option>
                            <option value="express-entry">Express Entry</option>
                            <option value="provincial-nominee">Provincial Nominee</option>
                            <option value="family">Family Sponsorship</option>
                            <option value="visitor">Visitor Visa</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Your Message</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- ... other sections ... -->

<?php include('includes/footer.php'); ?> 