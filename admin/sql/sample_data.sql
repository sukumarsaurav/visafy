-- ======================================================
-- SAMPLE DATA FOR FAQ SYSTEM
-- ======================================================

-- Sample FAQ Categories
INSERT INTO `faq_categories` (`name`, `icon`, `display_order`) VALUES
('General Questions', 'fas fa-question-circle', 1),
('Immigration Programs', 'fas fa-passport', 2),
('Application Process', 'fas fa-file-alt', 3),
('Fees & Costs', 'fas fa-dollar-sign', 4),
('After Arrival', 'fas fa-plane-arrival', 5);

-- Sample FAQ Items
INSERT INTO `faq_items` (`category_id`, `question`, `answer`, `display_order`) VALUES
(1, 'What services does CANEXT Immigration provide?', 'CANEXT Immigration provides comprehensive immigration consulting services, including eligibility assessments, application preparation, document review, and ongoing support throughout your immigration journey.', 1),
(1, 'Are your immigration consultants licensed?', 'Yes, all our immigration consultants are licensed by the Immigration Consultants of Canada Regulatory Council (ICCRC) and adhere to a strict code of professional ethics.', 2),
(1, 'How can I contact CANEXT Immigration?', 'You can contact us through our website contact form, by email at info@canext.ca, or by phone at +1 (123) 456-7890. Our office hours are Monday to Friday, 9:00 AM to 5:00 PM EST.', 3),

(2, 'What is Express Entry?', 'Express Entry is Canada\'s online immigration application management system for skilled workers. It manages applications for three federal economic immigration programs: Federal Skilled Worker Program, Federal Skilled Trades Program, and Canadian Experience Class.', 1),
(2, 'What is the Provincial Nominee Program (PNP)?', 'The Provincial Nominee Program allows Canadian provinces and territories to nominate individuals who wish to immigrate to Canada and who are interested in settling in a particular province. Each province has its own streams and criteria for nomination.', 2),
(2, 'Am I eligible for the Study Permit?', 'To be eligible for a study permit, you must have been accepted by a designated learning institution in Canada, prove you have enough money to pay for tuition fees and living expenses, and be a law-abiding citizen with no criminal record.', 3),

(3, 'How long does the immigration process take?', 'Processing times vary depending on the immigration program and your country of residence. Express Entry applications typically take 6-8 months, while other programs may take longer. We provide estimated timelines based on your specific situation during our consultation.', 1),
(3, 'What documents do I need for my application?', 'Required documents typically include passport, education credentials, language test results, proof of work experience, police certificates, and medical examination results. The specific requirements depend on the immigration program you\'re applying for.', 2),
(3, 'Can CANEXT help with document translation?', 'Yes, we offer document translation services for all immigration-related documents. All translations are done by certified translators as required by Immigration, Refugees and Citizenship Canada (IRCC).', 3),

(4, 'What are your service fees?', 'Our service fees vary depending on the complexity of your case and the services required. We provide transparent fee structures during our initial consultation, with no hidden costs. Payment plans are available to suit your financial situation.', 1),
(4, 'What government fees are required?', 'Government fees vary by immigration program. For Express Entry, the main fees include the application fee ($825 CAD), right of permanent residence fee ($500 CAD), and biometrics fee ($85 CAD). Additional fees may apply for family members.', 2),
(4, 'Do you offer refunds?', 'We offer partial refunds if you cancel our services before we begin working on your case. Once we have started the application process, refunds are provided on a pro-rated basis for work not yet completed. Full details are outlined in our service agreement.', 3),

(5, 'What should I do when I first arrive in Canada?', 'Upon arrival, you should apply for a Social Insurance Number (SIN), health card, open a bank account, find accommodation, and familiarize yourself with local transportation. We provide a comprehensive arrival guide to all our clients.', 1),
(5, 'How can I find housing in Canada?', 'We can help you connect with reliable real estate agents, provide information on rental websites, and offer guidance on neighborhoods based on your preferences and budget.', 2),
(5, 'What settlement services are available to newcomers?', 'Canada offers numerous settlement services, including language classes, employment assistance, housing help, and community integration programs. Many of these services are free for permanent residents and refugees.', 3);

-- ======================================================
-- SAMPLE DATA FOR BLOG SYSTEM
-- ======================================================

-- Sample Blog Categories
INSERT INTO `blog_categories` (`name`, `slug`, `icon`, `display_order`) VALUES
('Immigration News', 'immigration-news', 'fas fa-newspaper', 1),
('Success Stories', 'success-stories', 'fas fa-star', 2),
('Provincial Programs', 'provincial-programs', 'fas fa-map-marker-alt', 3),
('Study in Canada', 'study-in-canada', 'fas fa-graduation-cap', 4),
('Work in Canada', 'work-in-canada', 'fas fa-briefcase', 5);

-- Sample Blog Posts - Immigration News Category
INSERT INTO `blog_posts` (`category_id`, `title`, `slug`, `excerpt`, `content`, `featured_image`, `author`, `status`, `publish_date`) VALUES
(1, 'Canada to Welcome 500,000 New Immigrants Annually by 2025', 'canada-to-welcome-500000-immigrants-annually', 'The Canadian government has announced plans to significantly increase immigration targets over the next three years.', '<p>The Canadian government has announced a significant increase in immigration targets for the coming years, aiming to welcome 500,000 new permanent residents annually by 2025. This ambitious plan represents one of the largest immigration initiatives in Canadian history.</p><p>Immigration Minister Sean Fraser unveiled the new Immigration Levels Plan, which outlines a strategy to address critical labor shortages and support Canada\'s post-pandemic economic recovery. The plan includes:</p><ul><li>465,000 new permanent residents in 2023</li><li>485,000 new permanent residents in 2024</li><li>500,000 new permanent residents in 2025</li></ul><p>"Immigration is critical to ensuring Canada remains a growing, prosperous country with a strong healthcare system, well-supported elderly population, and robust workforce across all sectors," said Minister Fraser at a press conference.</p><p>The plan focuses on economic immigration programs, including Express Entry and the Provincial Nominee Program, which will account for approximately 60% of new admissions. Family reunification programs will also see increased targets, allowing more Canadians to reunite with their loved ones.</p><p>This announcement comes as Canada faces significant workforce challenges, with nearly one million job vacancies across the country and an aging population creating additional labor market pressures.</p><p>For prospective immigrants, this represents an excellent opportunity to realize their Canadian dreams. However, increased application volumes may also mean more competition for popular immigration programs.</p>', 'images/blog/immigration-news-1.jpg', 'Maria Rodriguez', 'published', NOW() - INTERVAL 2 DAY),

(1, 'Express Entry: CRS Score Drops to 475 in Latest Draw', 'express-entry-crs-score-drops', 'The latest Express Entry draw saw the CRS score requirement drop to its lowest point in six months.', '<p>In the latest Express Entry draw held on October 11, 2023, Immigration, Refugees and Citizenship Canada (IRCC) invited 3,500 candidates to apply for permanent residence with Comprehensive Ranking System (CRS) scores as low as 475.</p><p>This represents a significant drop from previous draws, where the CRS score cutoff had been hovering around 500 points. The decrease makes Canadian immigration more accessible to a wider pool of candidates.</p><p>Key details from the latest draw:</p><ul><li>Number of invitations issued: 3,500</li><li>Minimum CRS score requirement: 475</li><li>Tie-breaking rule: September 29, 2023, at 11:42:58 UTC</li></ul><p>The draw included candidates from all Express Entry programs, including the Federal Skilled Worker Program, Federal Skilled Trades Program, and Canadian Experience Class.</p><p>This drop in CRS scores may be attributed to the Canadian government\'s commitment to increasing immigration targets. As announced in the latest Immigration Levels Plan, Canada aims to welcome 500,000 new permanent residents annually by 2025, with a significant portion coming through economic immigration programs like Express Entry.</p><p>For prospective applicants, this trend suggests that now may be an opportune time to submit an Express Entry profile. Even candidates with moderate CRS scores may have improved chances of receiving an invitation to apply in the coming months.</p><p>Keep in mind that there are many ways to improve your CRS score, including obtaining additional education credentials, securing a job offer in Canada, improving language test scores, or gaining additional work experience.</p>', 'images/blog/immigration-news-2.jpg', 'David Thompson', 'published', NOW() - INTERVAL 5 DAY),

(1, 'New Pathways for International Students to Obtain Permanent Residence', 'new-pathways-international-students', 'Canada introduces new immigration pathways designed specifically for international students.', '<p>Immigration, Refugees and Citizenship Canada (IRCC) has announced new immigration pathways designed to help international students transition to permanent residence after completing their studies in Canada.</p><p>The new initiatives recognize the significant contributions international students make to Canada\'s economy, education system, and communities. They also acknowledge that international graduates often have the Canadian education, language proficiency, and work experience that help them integrate quickly into the Canadian labor market.</p><p>Key features of the new pathways include:</p><ol><li><strong>Extended Post-Graduation Work Permits (PGWP)</strong> - Eligible graduates can now apply for work permits valid for up to three years, regardless of the length of their study program.</li><li><strong>Canadian Experience Class adjustments</strong> - Reduced work experience requirements for international graduates applying through Express Entry\'s Canadian Experience Class.</li><li><strong>Provincial Nominee Program options</strong> - New international graduate streams in several Provincial Nominee Programs with simplified requirements.</li></ol><p>To qualify for these new pathways, international students must:</p><ul><li>Have graduated from a Designated Learning Institution (DLI)</li><li>Have completed a program of at least 8 months in duration</li><li>Have maintained valid immigration status during their studies</li></ul><p>International students currently account for approximately 25% of all new permanent residents to Canada, highlighting their importance to Canada\'s immigration strategy and future workforce.</p><p>"International students bring tremendous economic, cultural and social benefits to communities throughout Canada," said Immigration Minister Sean Fraser. "These new pathways will help more international graduates build their future in Canada, while addressing our labor market needs."</p>', 'images/blog/immigration-news-3.jpg', 'Sarah Johnson', 'published', NOW() - INTERVAL 10 DAY);

-- Add remaining blog posts and other sample data...

-- Insert default blog settings
INSERT INTO `blog_settings` (`setting_name`, `setting_value`) VALUES
('blog_title', 'CANEXT Immigration Blog'),
('blog_description', 'Latest news, tips and insights about Canadian immigration'),
('posts_per_page', '6'),
('allow_comments', 'yes'),
('moderate_comments', 'yes'),
('featured_category', '1');

-- Insert some sample tags
INSERT INTO `blog_tags` (`name`, `slug`) VALUES
('Express Entry', 'express-entry'),
('Study Permit', 'study-permit'),
('Work Permit', 'work-permit'),
('PNP', 'pnp'),
('LMIA', 'lmia'),
('Family Sponsorship', 'family-sponsorship'),
('Citizenship', 'citizenship'),
('Immigration News', 'immigration-news'),
('Success Stories', 'success-stories'),
('Canadian Experience Class', 'canadian-experience-class');

-- Update post_count for all categories
UPDATE blog_categories 
SET post_count = (SELECT COUNT(*) FROM blog_posts WHERE blog_posts.category_id = blog_categories.id); 