-- SQLite SQL Dump
-- Exported at: 2026-02-27T05:36:37+00:00

PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;

DROP TABLE IF EXISTS "announcements";
CREATE TABLE "announcements" ("id" integer primary key autoincrement not null, "created_by" integer, "title" varchar, "message_html" text, "media_type" varchar not null default 'none', "media_path" varchar, "youtube_url" varchar, "is_active" tinyint(1) not null default '1', "autoplay" tinyint(1) not null default '1', "start_at" datetime, "end_at" datetime, "created_at" datetime, "updated_at" datetime, foreign key("created_by") references "users"("id") on delete set null);

INSERT INTO "announcements" ("id", "created_by", "title", "message_html", "media_type", "media_path", "youtube_url", "is_active", "autoplay", "start_at", "end_at", "created_at", "updated_at") VALUES (1, 3, 'dehi', NULL, 'youtube', NULL, 'https://www.youtube.com/watch?v=OiDm3af5FxI', 0, 1, NULL, NULL, '2026-02-20 12:10:48', '2026-02-20 12:19:59');
INSERT INTO "announcements" ("id", "created_by", "title", "message_html", "media_type", "media_path", "youtube_url", "is_active", "autoplay", "start_at", "end_at", "created_at", "updated_at") VALUES (2, 3, 'dehi', NULL, 'youtube', NULL, 'https://www.youtube.com/watch?v=OiDm3af5FxI', 0, 1, '2026-02-20 12:15:00', '2026-02-20 12:21:00', '2026-02-20 12:15:58', '2026-02-20 12:20:12');
INSERT INTO "announcements" ("id", "created_by", "title", "message_html", "media_type", "media_path", "youtube_url", "is_active", "autoplay", "start_at", "end_at", "created_at", "updated_at") VALUES (3, 3, 'dehi', 'in frame video', 'image', 'uploads/announcements/5611f833-53a0-46c7-b1f0-62050200e251.jpg', NULL, 0, 1, '2026-02-20 12:21:00', '2026-02-20 12:27:00', '2026-02-20 12:21:35', '2026-02-21 04:17:42');
INSERT INTO "announcements" ("id", "created_by", "title", "message_html", "media_type", "media_path", "youtube_url", "is_active", "autoplay", "start_at", "end_at", "created_at", "updated_at") VALUES (4, 3, 'dehi', 'in frame video', 'image', 'uploads/announcements/a4d63e7c-bb62-4b17-818c-c714166f0fa5.jpg', NULL, 0, 1, '2026-02-20 12:21:00', '2026-02-20 12:27:00', '2026-02-20 12:21:36', '2026-02-21 04:15:11');

DROP TABLE IF EXISTS "cache";
CREATE TABLE "cache" ("key" varchar not null, "value" text not null, "expiration" integer not null, primary key ("key"));

DROP TABLE IF EXISTS "cache_locks";
CREATE TABLE "cache_locks" ("key" varchar not null, "owner" varchar not null, "expiration" integer not null, primary key ("key"));

DROP TABLE IF EXISTS "certificates";
CREATE TABLE "certificates" ("id" integer primary key autoincrement not null, "user_id" integer not null, "course_id" integer not null, "certificate_number" varchar not null, "issued_at" datetime not null, "pdf_path" varchar, foreign key("user_id") references "users"("id") on delete cascade, foreign key("course_id") references "courses"("id") on delete cascade);

INSERT INTO "certificates" ("id", "user_id", "course_id", "certificate_number", "issued_at", "pdf_path") VALUES (1, 4, 1, 'HOL-CL-2026-0104', '2026-02-26 22:47:29', 'certificates/HOL-CL-2026-0104.pdf');

DROP TABLE IF EXISTS "course_lessons";
CREATE TABLE "course_lessons" ("id" integer primary key autoincrement not null, "course_id" integer not null, "title" varchar not null, "content" text not null, "order" integer not null, "created_at" datetime, "updated_at" datetime, foreign key("course_id") references "courses"("id") on delete cascade);

INSERT INTO "course_lessons" ("id", "course_id", "title", "content", "order", "created_at", "updated_at") VALUES (1, 1, 'Understanding Continuous Learning', '<h2>Module 1: Understanding Continuous Learning</h2>
<p>Continuous learning is the disciplined habit of improving knowledge, instructional skill, and professional judgment throughout a teaching career. In modern education, static expertise is not enough because learner needs, curriculum priorities, technology tools, and assessment expectations evolve quickly. Teachers who practice continuous learning are better prepared to adapt instruction, solve classroom challenges, and deliver meaningful outcomes for students across different ability levels.</p>

<h3>Why Continuous Learning Matters in Teaching</h3>
<p>Teaching is a high-impact profession where daily decisions affect confidence, achievement, and long-term student growth. Continuous learners avoid professional stagnation by reviewing practice, testing better methods, and updating subject knowledge. This mindset supports stronger planning, more inclusive classroom management, and better communication with families and school leaders.</p>
<ul>
    <li>It improves lesson quality through regular refinement and reflection.</li>
    <li>It strengthens teacher confidence in new or unfamiliar situations.</li>
    <li>It supports evidence-based decisions instead of routine-based habits.</li>
    <li>It increases long-term employability and leadership readiness.</li>
    <li>It aligns teacher growth with student growth expectations.</li>
</ul>

<h3>Core Principles of a Continuous Learner</h3>
<p>A continuous learner is curious, accountable, and deliberate. Curiosity drives inquiry into what works and what does not. Accountability ensures that new learning is translated into visible instructional change. Deliberate practice means selecting specific goals, applying focused effort, and measuring progress against clear standards.</p>
<ul>
    <li><strong>Clarity:</strong> Define one professional learning goal at a time.</li>
    <li><strong>Consistency:</strong> Schedule weekly learning blocks and protect them.</li>
    <li><strong>Reflection:</strong> Review classroom outcomes to detect patterns.</li>
    <li><strong>Application:</strong> Turn theory into classroom routines quickly.</li>
    <li><strong>Feedback:</strong> Use student data and peer review to improve.</li>
</ul>

<h3>Learning Sources Teachers Should Use</h3>
<p>Effective teacher learning combines formal and informal sources. Formal sources include workshops, credential programs, and research journals. Informal sources include peer observation, professional communities, and post-lesson journaling. The strongest growth happens when teachers combine both types and connect each activity to a defined classroom problem.</p>
<ul>
    <li>Professional books and trusted education journals.</li>
    <li>Subject-specific webinars and micro-courses.</li>
    <li>Peer coaching and lesson study cycles.</li>
    <li>Student work analysis and formative assessment trends.</li>
    <li>Mentor feedback and school improvement plans.</li>
</ul>

<h3>Implementation Framework for the First 30 Days</h3>
<p>Start with a baseline self-assessment on planning, instruction, assessment, and classroom culture. Choose one competency to improve and define success indicators. Create a weekly plan with one learning input, one classroom experiment, and one reflection checkpoint. Document outcomes and identify what to continue, adjust, or stop.</p>
<ul>
    <li>Week 1: Diagnose needs and set measurable goals.</li>
    <li>Week 2: Learn one strategy and test it in one class section.</li>
    <li>Week 3: Collect evidence from student responses and outcomes.</li>
    <li>Week 4: Refine the strategy and scale to additional classes.</li>
</ul>

<p>Continuous learning is not an extra burden; it is the operating system of high-quality teaching. When teachers intentionally learn, students receive better questions, stronger explanations, clearer feedback, and more engaging classroom experiences.</p>', 1, '2026-02-26 22:09:08', '2026-02-26 22:09:08');
INSERT INTO "course_lessons" ("id", "course_id", "title", "content", "order", "created_at", "updated_at") VALUES (2, 1, 'Creating a Learning Culture', '<h2>Module 2: Creating a Learning Culture</h2>
<p>A learning culture is an environment where growth, inquiry, and improvement are normal expectations for both teachers and students. In schools with a strong learning culture, people ask better questions, share practices openly, and treat feedback as a tool for progress rather than criticism. Teachers play a central role in shaping this culture through routines, communication, and visible learning behaviors.</p>

<h3>Characteristics of a Strong Learning Culture</h3>
<p>Culture is built through repeated actions. It is not created by slogans, isolated workshops, or occasional meetings. A healthy learning culture is visible in how teams collaborate, how students respond to challenge, and how school leaders support experimentation. Teachers can influence this immediately by modeling reflection, encouraging risk-taking, and normalizing mistake-based learning.</p>
<ul>
    <li>Psychological safety for asking questions and admitting uncertainty.</li>
    <li>Shared goals connected to student learning outcomes.</li>
    <li>Regular routines for collaboration and practice-sharing.</li>
    <li>Constructive feedback with clear improvement targets.</li>
    <li>Recognition for progress, not only final performance.</li>
</ul>

<h3>Teacher Actions That Build Learning Culture</h3>
<p>Everyday teacher decisions shape whether learners feel safe to engage deeply. When teachers model curiosity, students are more willing to explore. When teachers use transparent criteria, students understand how to improve. When teachers discuss mistakes as learning data, classroom resilience increases.</p>
<ul>
    <li>Begin lessons with clear learning intentions and success criteria.</li>
    <li>Use think-aloud methods to model reasoning and revision.</li>
    <li>Invite student questions before giving final answers.</li>
    <li>Use exit tickets to collect evidence and adapt instruction.</li>
    <li>Hold short reflection routines at the end of each week.</li>
</ul>

<h3>Collaboration Structures for Teacher Teams</h3>
<p>Teacher learning accelerates in structured collaboration. Informal conversations are useful, but planned routines create consistent improvement. Teams should review student work together, co-design assessments, and analyze what instructional moves led to stronger evidence of learning.</p>
<ul>
    <li>Weekly professional learning community meetings with fixed agendas.</li>
    <li>Peer observations focused on one instructional target at a time.</li>
    <li>Lesson study cycles with planning, observation, and debrief.</li>
    <li>Shared digital repositories of tested lesson resources.</li>
    <li>Monthly data conversations tied to intervention planning.</li>
</ul>

<h3>Addressing Common Barriers</h3>
<p>Schools often face time pressure, inconsistent follow-through, and fear of judgment. Teachers can reduce these barriers by using short, repeatable routines and setting practical expectations. Improvement should be iterative, with one manageable focus area per cycle. Leaders and teachers should track progress publicly and celebrate evidence-based gains.</p>
<ul>
    <li>Use 20-minute collaboration blocks instead of waiting for long meetings.</li>
    <li>Focus on one shared priority each month.</li>
    <li>Use simple templates for planning, observation, and feedback.</li>
    <li>Track changes and outcomes in a visible team dashboard.</li>
</ul>

<p>Creating a learning culture is a strategic process. When teachers consistently reinforce inquiry, accountability, and collaboration, school improvement becomes sustainable and student outcomes become more predictable.</p>', 2, '2026-02-26 22:09:08', '2026-02-26 22:09:08');
INSERT INTO "course_lessons" ("id", "course_id", "title", "content", "order", "created_at", "updated_at") VALUES (3, 1, 'Becoming a Master Learner', '<h2>Module 3: Becoming a Master Learner</h2>
<p>A master learner is a teacher who learns with purpose, applies knowledge quickly, and continuously improves performance through evidence. Mastery is not about consuming more content; it is about learning the right things, at the right depth, and converting learning into measurable impact on student achievement. This module gives teachers a practical system to become intentional, efficient, and high-performing learners.</p>

<h3>Define Your Professional Learning Strategy</h3>
<p>Master learners start with a clear strategy rather than random professional development. They identify a priority area, understand their current baseline, and commit to a sequence of deliberate practice. A strategy should specify what to learn, why it matters, and how success will be measured in classroom outcomes.</p>
<ul>
    <li>Select one high-impact domain: instruction, assessment, or behavior support.</li>
    <li>Define specific indicators of success using student evidence.</li>
    <li>Set a timeline with milestones and weekly actions.</li>
    <li>Choose learning resources aligned to the target competency.</li>
    <li>Review progress with a mentor or peer accountability partner.</li>
</ul>

<h3>Use Deliberate Practice, Not Passive Exposure</h3>
<p>Reading and attending workshops are useful, but mastery requires active practice. Deliberate practice means isolating one skill, repeating it in real contexts, and refining it through feedback loops. For teachers, this may include questioning techniques, explanation clarity, differentiation methods, or formative assessment routines.</p>
<ul>
    <li>Break complex teaching skills into smaller trainable components.</li>
    <li>Practice one component repeatedly across multiple lessons.</li>
    <li>Collect evidence from student engagement and understanding.</li>
    <li>Adjust based on data, then repeat with improved precision.</li>
    <li>Document what changed and what impact followed.</li>
</ul>

<h3>Build Knowledge Management Habits</h3>
<p>Master learners maintain systems for organizing ideas, resources, and reflections. Without systems, insights are lost and growth becomes inconsistent. Teachers should create a simple personal knowledge framework that captures strategies, examples, and classroom outcomes for future reuse.</p>
<ul>
    <li>Maintain a weekly learning journal with action points.</li>
    <li>Tag resources by topic, grade band, and teaching purpose.</li>
    <li>Create a playbook of proven routines for recurring challenges.</li>
    <li>Track lesson adjustments linked to student results.</li>
    <li>Review and consolidate learning monthly.</li>
</ul>

<h3>Measure Progress With Evidence</h3>
<p>Professional growth becomes meaningful when it is visible in student outcomes. Teachers should define practical metrics such as participation rates, quality of student responses, assessment gains, and reduced misconceptions. Evidence should be reviewed regularly to decide whether to continue, adapt, or replace a strategy.</p>
<ul>
    <li>Use before-and-after comparisons for targeted interventions.</li>
    <li>Track formative data by objective, not just by lesson.</li>
    <li>Collect student voice on clarity and engagement.</li>
    <li>Evaluate progress every two weeks and reset goals as needed.</li>
</ul>

<p>Becoming a master learner is a long-term professional identity, not a short-term activity. Teachers who combine strategy, practice, systems, and evidence consistently improve faster and lead stronger learning experiences for every student.</p>', 3, '2026-02-26 22:09:08', '2026-02-26 22:09:08');
INSERT INTO "course_lessons" ("id", "course_id", "title", "content", "order", "created_at", "updated_at") VALUES (4, 1, 'Receptiveness to Learning & Behaviour Modification', '<h2>Module 4: Receptiveness to Learning and Behaviour Modification</h2>
<p>Receptiveness to learning is the willingness to update beliefs, adopt better methods, and respond constructively to evidence. For teachers, this quality is critical because classroom realities often challenge assumptions. Behaviour modification is the process of turning new insights into repeatable professional habits. Together, these two capabilities help educators move from awareness to sustained improvement.</p>

<h3>Understanding Receptiveness in Professional Growth</h3>
<p>Receptive teachers do not defend ineffective routines when data shows gaps. They remain open to coaching, student feedback, and peer observation. Receptiveness is not passive agreement; it is active evaluation and disciplined adjustment. This mindset increases instructional agility and supports inclusive teaching for diverse student needs.</p>
<ul>
    <li>Accept feedback as performance data, not personal judgment.</li>
    <li>Distinguish between preference and effectiveness.</li>
    <li>Ask clarifying questions before rejecting new approaches.</li>
    <li>Test alternatives in controlled, measurable classroom trials.</li>
    <li>Reflect on emotional triggers that block improvement.</li>
</ul>

<h3>Behaviour Modification Framework for Teachers</h3>
<p>Changing professional behavior requires structure. Teachers can use a four-step cycle: identify target behavior, design cues and routines, monitor implementation, and reinforce consistency. For example, if a teacher wants stronger checks for understanding, the target behavior may be using two formative checks in every lesson segment.</p>
<ul>
    <li><strong>Step 1:</strong> Identify one high-leverage behavior to change.</li>
    <li><strong>Step 2:</strong> Attach the behavior to a clear cue in lesson flow.</li>
    <li><strong>Step 3:</strong> Track completion daily with a simple checklist.</li>
    <li><strong>Step 4:</strong> Review outcomes weekly and refine execution.</li>
</ul>

<h3>Using Feedback to Support Behaviour Change</h3>
<p>Feedback is most useful when it is specific, timely, and actionable. Teachers should seek feedback from multiple sources, including mentors, peers, and students. The goal is to identify which behaviors improve understanding, participation, and retention. Feedback should lead to one clear action, not a vague intention.</p>
<ul>
    <li>Ask observers to focus on one predefined behavior.</li>
    <li>Use student exit responses to validate instructional adjustments.</li>
    <li>Translate feedback into one next-step experiment.</li>
    <li>Re-observe after implementation to confirm progress.</li>
    <li>Celebrate consistency before adding a new behavior target.</li>
</ul>

<h3>Maintaining Change Over Time</h3>
<p>Most behaviour change fails because teachers attempt too many changes at once. Sustainable growth requires focus, repetition, and accountability. Build routines into planning templates, use reminders, and work with a peer partner to review implementation data. Once a behavior becomes automatic, move to the next improvement target.</p>
<ul>
    <li>Limit active behavior targets to one or two at a time.</li>
    <li>Use weekly review sessions to prevent regression.</li>
    <li>Document successful routines in a personal teaching playbook.</li>
    <li>Use short reflection prompts after each teaching day.</li>
</ul>

<p>Receptiveness and behaviour modification turn professional intent into visible classroom impact. Teachers who practice both consistently become more adaptive, more effective, and more capable of leading meaningful change in school communities.</p>', 4, '2026-02-26 22:09:08', '2026-02-26 22:09:08');
INSERT INTO "course_lessons" ("id", "course_id", "title", "content", "order", "created_at", "updated_at") VALUES (5, 1, 'Mastering Creativity', '<h2>Module 5: Mastering Creativity</h2>
<p>Creativity in education is the ability to design learning experiences that are engaging, meaningful, and responsive to student needs while still aligned with curriculum goals. Creative teaching is not entertainment without direction; it is purposeful innovation that improves understanding, retention, and student motivation. This module helps teachers build a repeatable process for generating and applying creative instructional ideas.</p>

<h3>The Role of Creativity in Modern Classrooms</h3>
<p>Students learn better when instruction connects with curiosity, real-world relevance, and active thinking. Creative teachers vary instructional methods, use multiple representations, and design tasks that require reasoning rather than memorization alone. Creativity also helps teachers reach mixed-ability classes by providing flexible pathways to the same learning objective.</p>
<ul>
    <li>It increases student attention and participation.</li>
    <li>It improves conceptual understanding through diverse approaches.</li>
    <li>It supports differentiated learning for varied readiness levels.</li>
    <li>It builds student confidence in problem solving and expression.</li>
    <li>It makes learning memorable and transferable beyond exams.</li>
</ul>

<h3>Creative Design Principles for Teachers</h3>
<p>Creative teaching can be systematized using design principles. Begin with a clear learning objective, then choose engaging entry points, interactive tasks, and authentic outputs. Keep creativity aligned with outcomes by defining what students should know, do, and produce by the end of each lesson.</p>
<ul>
    <li>Start with objective clarity before selecting activities.</li>
    <li>Use varied modalities: visual, verbal, kinesthetic, collaborative.</li>
    <li>Design tasks with challenge and choice.</li>
    <li>Connect concepts to real-life contexts and local issues.</li>
    <li>Embed reflection so students articulate learning.</li>
</ul>

<h3>Practical Creativity Toolkit</h3>
<p>Teachers can use practical tools to generate ideas quickly and avoid repetitive lesson patterns. Brainstorming frameworks, concept mapping, scenario prompts, and mini-project structures help create rich learning tasks without excessive preparation time.</p>
<ul>
    <li><strong>SCAMPER:</strong> Modify existing activities using prompts such as substitute, combine, and adapt.</li>
    <li><strong>Think-Design-Test:</strong> Prototype one activity, test it, and refine immediately.</li>
    <li><strong>Question Ladder:</strong> Move from recall questions to analysis and creation.</li>
    <li><strong>Choice Boards:</strong> Offer multiple pathways for demonstrating mastery.</li>
    <li><strong>Micro-projects:</strong> Use short authentic tasks with clear deliverables.</li>
</ul>

<h3>Measuring Creative Instructional Impact</h3>
<p>Creative teaching should improve outcomes, not only classroom energy. Evaluate impact by tracking student engagement quality, depth of responses, quality of student products, and assessment transfer. Keep what works, refine what underperforms, and share successful practices with peers.</p>
<ul>
    <li>Use rubrics to assess originality, understanding, and application.</li>
    <li>Collect student reflections on challenge and clarity.</li>
    <li>Compare performance on traditional and creative assessments.</li>
    <li>Review evidence every two weeks and iterate quickly.</li>
</ul>

<p>Mastering creativity is a professional advantage that combines pedagogical rigor with innovation. Teachers who apply creativity strategically produce stronger learning experiences, deeper student thinking, and more resilient classroom engagement over time.</p>', 5, '2026-02-26 22:09:08', '2026-02-26 22:09:08');

DROP TABLE IF EXISTS "courses";
CREATE TABLE "courses" ("id" integer primary key autoincrement not null, "title" varchar not null, "slug" varchar not null, "description" text not null, "requirements" text, "passing_score" integer not null default '70', "is_active" tinyint(1) not null default '1', "created_at" datetime, "updated_at" datetime);

INSERT INTO "courses" ("id", "title", "slug", "description", "requirements", "passing_score", "is_active", "created_at", "updated_at") VALUES (1, 'Continuous Learning - Becoming a Star Educator', 'continuous-learning', 'Continuous Learning - Becoming a Star Educator is a professional development journey designed for teachers who want to stay relevant, reflective, and future-ready in rapidly changing classrooms. The course helps educators build a disciplined learning mindset, create supportive environments for growth, and strengthen practical habits that improve teaching quality over time. Through five applied modules, participants learn how to diagnose personal skill gaps, develop learning systems, transform feedback into measurable improvement, and consistently innovate lesson experiences for diverse learners. The course bridges mindset and practice by combining reflective frameworks, collaboration strategies, behavior-change methods, and creativity tools that can be implemented immediately in school settings. By the end of the program, teachers are equipped to lead by example, adapt to new educational demands, and sustain long-term excellence in student-centered instruction.', 'Proficient in English', 70, 1, '2026-02-26 22:09:07', '2026-02-26 22:09:07');

DROP TABLE IF EXISTS "donations";
CREATE TABLE "donations" ("id" integer primary key autoincrement not null, "user_id" integer, "donor_name" varchar not null, "donor_email" varchar not null, "amount" numeric not null, "currency" varchar not null default ('USD'), "donation_type" varchar not null default ('one_time'), "status" varchar not null default ('pending'), "stripe_checkout_session_id" varchar, "stripe_payment_intent_id" varchar, "stripe_subscription_id" varchar, "receipt_url" varchar, "impact_snapshot" text, "created_at" datetime, "updated_at" datetime, "payment_proof_path" varchar, "verified_at" datetime, "verified_by" integer, "receipt_number" varchar, "receipt_sent_at" datetime, "receipt_pdf_path" varchar, foreign key("user_id") references users("id") on delete set null on update no action, foreign key("verified_by") references "users"("id") on delete set null);

INSERT INTO "donations" ("id", "user_id", "donor_name", "donor_email", "amount", "currency", "donation_type", "status", "stripe_checkout_session_id", "stripe_payment_intent_id", "stripe_subscription_id", "receipt_url", "impact_snapshot", "created_at", "updated_at", "payment_proof_path", "verified_at", "verified_by", "receipt_number", "receipt_sent_at", "receipt_pdf_path") VALUES (1, 2, 'ibrahim', 'shahzaibbaig294@gmail.com', 300, 'USD', 'one_time', 'pending', NULL, NULL, NULL, NULL, '{"amount_usd":300,"devices_supported":3,"teacher_training_hours":30,"classroom_upgrade_contribution":60}', '2026-02-20 12:01:35', '2026-02-20 12:01:35', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO "donations" ("id", "user_id", "donor_name", "donor_email", "amount", "currency", "donation_type", "status", "stripe_checkout_session_id", "stripe_payment_intent_id", "stripe_subscription_id", "receipt_url", "impact_snapshot", "created_at", "updated_at", "payment_proof_path", "verified_at", "verified_by", "receipt_number", "receipt_sent_at", "receipt_pdf_path") VALUES (2, 2, 'ibrahim', 'shahzaibbaig294@gmail.com', 300, 'USD', 'one_time', 'pending', NULL, NULL, NULL, NULL, '{"amount_usd":300,"devices_supported":3,"teacher_training_hours":30,"classroom_upgrade_contribution":60}', '2026-02-20 12:01:43', '2026-02-20 12:01:43', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO "donations" ("id", "user_id", "donor_name", "donor_email", "amount", "currency", "donation_type", "status", "stripe_checkout_session_id", "stripe_payment_intent_id", "stripe_subscription_id", "receipt_url", "impact_snapshot", "created_at", "updated_at", "payment_proof_path", "verified_at", "verified_by", "receipt_number", "receipt_sent_at", "receipt_pdf_path") VALUES (3, 2, 'ibrahim', 'shahzaibbaig294@gmail.com', 300, 'USD', 'one_time', 'pending', NULL, NULL, NULL, NULL, '{"amount_usd":300,"devices_supported":3,"teacher_training_hours":30,"classroom_upgrade_contribution":60}', '2026-02-20 12:01:47', '2026-02-20 12:01:47', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO "donations" ("id", "user_id", "donor_name", "donor_email", "amount", "currency", "donation_type", "status", "stripe_checkout_session_id", "stripe_payment_intent_id", "stripe_subscription_id", "receipt_url", "impact_snapshot", "created_at", "updated_at", "payment_proof_path", "verified_at", "verified_by", "receipt_number", "receipt_sent_at", "receipt_pdf_path") VALUES (4, 4, 'ibrahim', 'shahzaibbaig294@gmail.com', 1000, 'USD', 'one_time', 'pending', NULL, NULL, NULL, NULL, '{"amount_usd":1000,"devices_supported":12,"teacher_training_hours":100,"classroom_upgrade_contribution":200}', '2026-02-24 07:18:48', '2026-02-24 07:18:48', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO "donations" ("id", "user_id", "donor_name", "donor_email", "amount", "currency", "donation_type", "status", "stripe_checkout_session_id", "stripe_payment_intent_id", "stripe_subscription_id", "receipt_url", "impact_snapshot", "created_at", "updated_at", "payment_proof_path", "verified_at", "verified_by", "receipt_number", "receipt_sent_at", "receipt_pdf_path") VALUES (5, 4, 'ibrahim', 'shahzaibbaig294@gmail.com', 1000, 'USD', 'one_time', 'pending', NULL, NULL, NULL, NULL, '{"amount_usd":1000,"devices_supported":12,"teacher_training_hours":100,"classroom_upgrade_contribution":200}', '2026-02-24 07:18:58', '2026-02-24 07:18:58', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO "donations" ("id", "user_id", "donor_name", "donor_email", "amount", "currency", "donation_type", "status", "stripe_checkout_session_id", "stripe_payment_intent_id", "stripe_subscription_id", "receipt_url", "impact_snapshot", "created_at", "updated_at", "payment_proof_path", "verified_at", "verified_by", "receipt_number", "receipt_sent_at", "receipt_pdf_path") VALUES (6, 2, 'ibrahim', 'shahzaibbaig294@gmail.com', 100, 'USD', 'one_time', 'pending', NULL, NULL, NULL, NULL, '{"amount_usd":100,"devices_supported":1,"teacher_training_hours":10,"classroom_upgrade_contribution":20}', '2026-02-25 05:49:47', '2026-02-25 05:49:47', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO "donations" ("id", "user_id", "donor_name", "donor_email", "amount", "currency", "donation_type", "status", "stripe_checkout_session_id", "stripe_payment_intent_id", "stripe_subscription_id", "receipt_url", "impact_snapshot", "created_at", "updated_at", "payment_proof_path", "verified_at", "verified_by", "receipt_number", "receipt_sent_at", "receipt_pdf_path") VALUES (9, 4, 'shahzaib', 'shahzaib.baig@gmail.com', 600, 'USD', 'one_time', 'verified', NULL, NULL, NULL, NULL, '{"amount_usd":600,"devices_supported":8,"teacher_training_hours":60,"classroom_upgrade_contribution":100}', '2026-02-25 15:54:21', '2026-02-25 15:56:48', 'donations/payment-proofs/images/kIlRf1zEkp0G8KvavLsspA0nfKdVVVISXs96kUjq.jpg', '2026-02-25 15:56:43', 3, 'HOL-2026-000009', '2026-02-25 15:56:48', NULL);
INSERT INTO "donations" ("id", "user_id", "donor_name", "donor_email", "amount", "currency", "donation_type", "status", "stripe_checkout_session_id", "stripe_payment_intent_id", "stripe_subscription_id", "receipt_url", "impact_snapshot", "created_at", "updated_at", "payment_proof_path", "verified_at", "verified_by", "receipt_number", "receipt_sent_at", "receipt_pdf_path") VALUES (10, 4, 'shahzaib', 'tubashahzaib@gmail.com', 700, 'USD', 'one_time', 'verified', NULL, NULL, NULL, NULL, '{"amount_usd":700,"devices_supported":9,"teacher_training_hours":70,"classroom_upgrade_contribution":100}', '2026-02-25 16:01:55', '2026-02-25 16:02:53', 'donations/payment-proofs/images/VDqljmJlLkrd6vHnSxAAefSIjGwyJ6BlcJTijeIj.jpg', '2026-02-25 16:02:47', 3, 'HOL-2026-000010', '2026-02-25 16:02:53', NULL);
INSERT INTO "donations" ("id", "user_id", "donor_name", "donor_email", "amount", "currency", "donation_type", "status", "stripe_checkout_session_id", "stripe_payment_intent_id", "stripe_subscription_id", "receipt_url", "impact_snapshot", "created_at", "updated_at", "payment_proof_path", "verified_at", "verified_by", "receipt_number", "receipt_sent_at", "receipt_pdf_path") VALUES (11, 4, 'shahzaib', 'shahzaib.baig@gmail.com', 900, 'USD', 'one_time', 'verified', NULL, NULL, NULL, NULL, '{"amount_usd":900,"devices_supported":12,"teacher_training_hours":90,"classroom_upgrade_contribution":100}', '2026-02-25 16:09:59', '2026-02-25 16:13:03', 'donations/payment-proofs/images/kiFmydLfZOEETwVNLLsgiK5uIFIHMs6UZJly0r3l.png', '2026-02-25 16:12:57', 3, 'HOL-2026-000011', '2026-02-25 16:13:03', NULL);
INSERT INTO "donations" ("id", "user_id", "donor_name", "donor_email", "amount", "currency", "donation_type", "status", "stripe_checkout_session_id", "stripe_payment_intent_id", "stripe_subscription_id", "receipt_url", "impact_snapshot", "created_at", "updated_at", "payment_proof_path", "verified_at", "verified_by", "receipt_number", "receipt_sent_at", "receipt_pdf_path") VALUES (12, 4, 'shahzaib', 'shahzaib.baig@gmail.com', 900, 'USD', 'one_time', 'verified', NULL, NULL, NULL, NULL, '{"amount_usd":900,"devices_supported":12,"teacher_training_hours":90,"classroom_upgrade_contribution":100}', '2026-02-26 05:46:41', '2026-02-26 05:47:40', 'donations/payment-proofs/images/Gw9peQGA2aCcDPJ9gPXp0NRXlTnxVpMRJyjmzTjD.png', '2026-02-26 05:47:25', 3, 'HOL-2026-000012', '2026-02-26 05:47:40', NULL);

DROP TABLE IF EXISTS "failed_jobs";
CREATE TABLE "failed_jobs" ("id" integer primary key autoincrement not null, "uuid" varchar not null, "connection" text not null, "queue" text not null, "payload" text not null, "exception" text not null, "failed_at" datetime not null default CURRENT_TIMESTAMP);

DROP TABLE IF EXISTS "job_batches";
CREATE TABLE "job_batches" ("id" varchar not null, "name" varchar not null, "total_jobs" integer not null, "pending_jobs" integer not null, "failed_jobs" integer not null, "failed_job_ids" text not null, "options" text, "cancelled_at" integer, "created_at" integer not null, "finished_at" integer, primary key ("id"));

DROP TABLE IF EXISTS "jobs";
CREATE TABLE "jobs" ("id" integer primary key autoincrement not null, "queue" varchar not null, "payload" text not null, "attempts" integer not null, "reserved_at" integer, "available_at" integer not null, "created_at" integer not null);

DROP TABLE IF EXISTS "lessons";
CREATE TABLE "lessons" ("id" integer primary key autoincrement not null, "user_id" integer not null, "volunteer_id" integer, "title" varchar not null, "subject" varchar not null, "grade_min" integer not null, "grade_max" integer not null, "lesson_type" varchar not null, "delivery_mode" varchar not null, "youtube_url" varchar, "video_path" varchar, "document_path" varchar, "description" text not null, "learning_objectives" text, "language" varchar not null default 'English', "duration_minutes" integer, "status" varchar not null default 'draft', "reviewed_by" integer, "reviewed_at" datetime, "review_notes" text, "published_at" datetime, "views_count" integer not null default '0', "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "users"("id") on delete cascade, foreign key("volunteer_id") references "volunteers"("id") on delete set null, foreign key("reviewed_by") references "users"("id") on delete set null);

DROP TABLE IF EXISTS "migrations";
CREATE TABLE "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null);

INSERT INTO "migrations" ("id", "migration", "batch") VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (4, '2026_02_17_000001_create_schools_table', 1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (5, '2026_02_17_000002_create_donations_table', 2);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (6, '2026_02_18_000003_add_role_to_users_table', 2);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (7, '2026_02_20_000004_create_announcements_table', 3);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (8, '2026_02_21_000005_add_teacher_fields_to_users_table', 4);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (9, '2026_02_21_000006_create_volunteers_table', 4);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (10, '2026_02_21_060940_create_lessons_table', 5);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (11, '2026_02_21_000007_add_profile_fields_to_volunteers_table', 6);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (12, '2026_02_24_000008_create_volunteer_documents_table', 7);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (13, '2026_02_24_000009_add_payment_proof_path_to_donations_table', 8);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (14, '2026_02_24_000010_add_logo_path_to_schools_table', 9);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (15, '2026_02_24_000011_add_city_to_schools_table', 10);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (16, '2026_02_25_145917_add_verification_receipt_fields_to_donations_table', 11);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (17, '2026_02_25_150059_add_verification_receipt_fields_to_donations_table', 11);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (18, '2026_02_25_154323_add_verification_receipt_fields_to_donations_table', 12);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (19, '2026_02_26_215936_create_courses_table', 13);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (20, '2026_02_26_215938_create_course_lessons_table', 13);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (21, '2026_02_26_215939_create_quizzes_table', 13);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (22, '2026_02_26_215940_create_quiz_questions_table', 13);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (23, '2026_02_26_215942_create_quiz_options_table', 13);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (24, '2026_02_26_215943_create_quiz_attempts_table', 13);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (25, '2026_02_26_215945_create_certificates_table', 13);

DROP TABLE IF EXISTS "password_reset_tokens";
CREATE TABLE "password_reset_tokens" ("email" varchar not null, "token" varchar not null, "created_at" datetime, primary key ("email"));

DROP TABLE IF EXISTS "quiz_attempts";
CREATE TABLE "quiz_attempts" ("id" integer primary key autoincrement not null, "user_id" integer not null, "quiz_id" integer not null, "score" integer not null, "passed" tinyint(1) not null, "attempted_at" datetime not null, foreign key("user_id") references "users"("id") on delete cascade, foreign key("quiz_id") references "quizzes"("id") on delete cascade);

INSERT INTO "quiz_attempts" ("id", "user_id", "quiz_id", "score", "passed", "attempted_at") VALUES (1, 4, 1, 100, 1, '2026-02-26 22:22:52');
INSERT INTO "quiz_attempts" ("id", "user_id", "quiz_id", "score", "passed", "attempted_at") VALUES (2, 4, 1, 100, 1, '2026-02-26 22:28:41');
INSERT INTO "quiz_attempts" ("id", "user_id", "quiz_id", "score", "passed", "attempted_at") VALUES (3, 4, 1, 100, 1, '2026-02-26 22:36:26');
INSERT INTO "quiz_attempts" ("id", "user_id", "quiz_id", "score", "passed", "attempted_at") VALUES (4, 4, 1, 100, 1, '2026-02-26 22:40:23');
INSERT INTO "quiz_attempts" ("id", "user_id", "quiz_id", "score", "passed", "attempted_at") VALUES (5, 4, 1, 100, 1, '2026-02-26 22:47:29');

DROP TABLE IF EXISTS "quiz_options";
CREATE TABLE "quiz_options" ("id" integer primary key autoincrement not null, "question_id" integer not null, "option_text" varchar not null, "is_correct" tinyint(1) not null default '0', foreign key("question_id") references "quiz_questions"("id") on delete cascade);

INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (1, 1, 'It reduces the need for lesson planning.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (2, 1, 'It helps teachers adapt and improve student outcomes over time.', 1);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (3, 1, 'It guarantees perfect exam scores immediately.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (4, 1, 'It replaces all classroom management responsibilities.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (5, 2, 'My teaching ability is fixed and cannot improve much.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (6, 2, 'Student failure means I should not try new methods.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (7, 2, 'I can improve my teaching through effort, reflection, and feedback.', 1);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (8, 2, 'Only experienced teachers can change instructional outcomes.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (9, 3, 'Avoiding classroom observation to prevent discomfort.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (10, 3, 'Treating mistakes as data for improvement.', 1);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (11, 3, 'Limiting collaboration to annual meetings only.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (12, 3, 'Focusing only on final results without reflection.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (13, 4, 'Collecting many resources without applying them.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (14, 4, 'Practicing one instructional skill deliberately and measuring impact.', 1);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (15, 4, 'Changing multiple strategies daily without tracking results.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (16, 4, 'Avoiding peer feedback to maintain personal style.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (17, 5, 'By making lessons entertaining without objectives.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (18, 5, 'By replacing curriculum goals with random activities.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (19, 5, 'By designing engaging methods that still align with clear outcomes.', 1);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (20, 5, 'By removing all structure from classroom tasks.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (21, 6, 'Identify one high-impact behavior to change.', 1);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (22, 6, 'Change all weak habits at once.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (23, 6, 'Wait for annual appraisals before making adjustments.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (24, 6, 'Ignore classroom evidence to reduce stress.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (25, 7, 'Rejecting all feedback that challenges current routines.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (26, 7, 'Treating feedback as personal criticism and avoiding it.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (27, 7, 'Asking clarifying questions and testing suggested improvements.', 1);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (28, 7, 'Accepting feedback only from students, never colleagues.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (29, 8, 'No routine, learning only when problems escalate.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (30, 8, 'A cycle of learning, classroom application, and reflection.', 1);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (31, 8, 'Reading theory only without classroom experimentation.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (32, 8, 'Switching goals every day without evidence tracking.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (33, 9, 'Private lesson planning with no sharing of outcomes.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (34, 9, 'Peer observation and joint review of student work.', 1);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (35, 9, 'Avoiding team meetings to save preparation time.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (36, 9, 'Comparing teachers only by test rankings.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (37, 10, 'To confirm whether changed practices improve student learning.', 1);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (38, 10, 'To eliminate the need for teacher reflection.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (39, 10, 'To focus only on attendance and punctuality.', 0);
INSERT INTO "quiz_options" ("id", "question_id", "option_text", "is_correct") VALUES (40, 10, 'To replace classroom assessments completely.', 0);

DROP TABLE IF EXISTS "quiz_questions";
CREATE TABLE "quiz_questions" ("id" integer primary key autoincrement not null, "quiz_id" integer not null, "question" text not null, "marks" integer not null default '1', "created_at" datetime, "updated_at" datetime, foreign key("quiz_id") references "quizzes"("id") on delete cascade);

INSERT INTO "quiz_questions" ("id", "quiz_id", "question", "marks", "created_at", "updated_at") VALUES (1, 1, 'Why is continuous learning essential for teachers in modern classrooms?', 1, '2026-02-26 22:11:27', '2026-02-26 22:11:27');
INSERT INTO "quiz_questions" ("id", "quiz_id", "question", "marks", "created_at", "updated_at") VALUES (2, 1, 'Which statement best reflects a growth mindset in teaching practice?', 1, '2026-02-26 22:11:27', '2026-02-26 22:11:27');
INSERT INTO "quiz_questions" ("id", "quiz_id", "question", "marks", "created_at", "updated_at") VALUES (3, 1, 'What is a core feature of a strong learning culture in a school?', 1, '2026-02-26 22:11:27', '2026-02-26 22:11:27');
INSERT INTO "quiz_questions" ("id", "quiz_id", "question", "marks", "created_at", "updated_at") VALUES (4, 1, 'Which behavior is most aligned with a master learner teacher?', 1, '2026-02-26 22:11:27', '2026-02-26 22:11:27');
INSERT INTO "quiz_questions" ("id", "quiz_id", "question", "marks", "created_at", "updated_at") VALUES (5, 1, 'How does creativity improve teaching and learning quality?', 1, '2026-02-26 22:11:27', '2026-02-26 22:11:27');
INSERT INTO "quiz_questions" ("id", "quiz_id", "question", "marks", "created_at", "updated_at") VALUES (6, 1, 'What is the first step in effective behaviour modification for teachers?', 1, '2026-02-26 22:11:27', '2026-02-26 22:11:27');
INSERT INTO "quiz_questions" ("id", "quiz_id", "question", "marks", "created_at", "updated_at") VALUES (7, 1, 'Which response shows receptiveness to professional feedback?', 1, '2026-02-26 22:11:27', '2026-02-26 22:11:27');
INSERT INTO "quiz_questions" ("id", "quiz_id", "question", "marks", "created_at", "updated_at") VALUES (8, 1, 'Which weekly routine best supports continuous learning?', 1, '2026-02-26 22:11:27', '2026-02-26 22:11:27');
INSERT INTO "quiz_questions" ("id", "quiz_id", "question", "marks", "created_at", "updated_at") VALUES (9, 1, 'What best demonstrates collaborative learning culture among teachers?', 1, '2026-02-26 22:11:27', '2026-02-26 22:11:27');
INSERT INTO "quiz_questions" ("id", "quiz_id", "question", "marks", "created_at", "updated_at") VALUES (10, 1, 'What is the main purpose of measuring professional growth with evidence?', 1, '2026-02-26 22:11:27', '2026-02-26 22:11:27');

DROP TABLE IF EXISTS "quizzes";
CREATE TABLE "quizzes" ("id" integer primary key autoincrement not null, "course_id" integer not null, "title" varchar not null, "total_marks" integer not null, "created_at" datetime, "updated_at" datetime, foreign key("course_id") references "courses"("id") on delete cascade);

INSERT INTO "quizzes" ("id", "course_id", "title", "total_marks", "created_at", "updated_at") VALUES (1, 1, 'Continuous Learning Final Assessment', 10, '2026-02-26 22:11:27', '2026-02-26 22:11:27');

DROP TABLE IF EXISTS "schools";
CREATE TABLE "schools" ("id" integer primary key autoincrement not null, "school_name" varchar not null, "principal_name" varchar not null, "contact_email" varchar not null, "phone_number" varchar not null, "address" text not null, "needs" text not null, "status" varchar not null default 'pending', "created_at" datetime, "updated_at" datetime, "logo_path" varchar, "city" varchar);

DROP TABLE IF EXISTS "sessions";
CREATE TABLE "sessions" ("id" varchar not null, "user_id" integer, "ip_address" varchar, "user_agent" text, "payload" text not null, "last_activity" integer not null, primary key ("id"));

DROP TABLE IF EXISTS "users";
CREATE TABLE "users" ("id" integer primary key autoincrement not null, "name" varchar not null, "email" varchar not null, "email_verified_at" datetime, "password" varchar not null, "remember_token" varchar, "created_at" datetime, "updated_at" datetime, "role" varchar not null default 'donor', "is_teacher" tinyint(1) not null default '0');

INSERT INTO "users" ("id", "name", "email", "email_verified_at", "password", "remember_token", "created_at", "updated_at", "role", "is_teacher") VALUES (1, 'Signup Test', 'signup-test@example.com', NULL, '$2y$12$Si38z8iVZY6rHJEqEr6ODOwJ6G/TM6hEWuIbyJg0ert2/hQe5Eiou', NULL, '2026-02-20 11:43:23', '2026-02-20 11:43:23', 'donor', 0);
INSERT INTO "users" ("id", "name", "email", "email_verified_at", "password", "remember_token", "created_at", "updated_at", "role", "is_teacher") VALUES (2, 'Shahzaib Baig', 'shahzaibbaig294@gmail.com', NULL, '$2y$12$XVoK1MYJl2fqyE.w0LPV8..HTCMzZWWQjW6cVaqXiJmnPEvOTMHfe', NULL, '2026-02-20 11:43:59', '2026-02-20 11:43:59', 'donor', 0);
INSERT INTO "users" ("id", "name", "email", "email_verified_at", "password", "remember_token", "created_at", "updated_at", "role", "is_teacher") VALUES (3, 'parizay', 'parizay@houroflight.com', NULL, '$2y$12$L.RQp300SS/L6Z7.LblXdeeaahjoGLxriXaKbfJrHkRDzGYXAHnH.', NULL, '2026-02-20 11:55:13', '2026-02-20 11:55:13', 'admin', 0);
INSERT INTO "users" ("id", "name", "email", "email_verified_at", "password", "remember_token", "created_at", "updated_at", "role", "is_teacher") VALUES (4, 'Muhammad Shahzaib Baig', 'shahzaib.baig@gmail.com', NULL, '$2y$12$uF6kzKmM0EqmMVcffwMih.A/LUzBWfhLQyYhuVtq2OuWAYKyjb7Va', NULL, '2026-02-21 05:21:13', '2026-02-21 05:21:13', 'volunteer_teacher', 1);
INSERT INTO "users" ("id", "name", "email", "email_verified_at", "password", "remember_token", "created_at", "updated_at", "role", "is_teacher") VALUES (5, 'shahzaib baig', 'shahzaibbaig2002@yahoo.com', NULL, '$2y$12$wdysFLNGm7rq/Ga/1yDwZu6m34gJIebS.GybHdj9f./QmHg.BWbWG', NULL, '2026-02-21 12:00:17', '2026-02-21 12:00:17', 'volunteer_teacher', 1);

DROP TABLE IF EXISTS "volunteer_documents";
CREATE TABLE "volunteer_documents" ("id" integer primary key autoincrement not null, "volunteer_id" integer not null, "category" varchar not null default 'certificate_award', "original_name" varchar not null, "file_path" varchar not null, "mime_type" varchar, "file_size" integer, "created_at" datetime, "updated_at" datetime, foreign key("volunteer_id") references "volunteers"("id") on delete cascade);

DROP TABLE IF EXISTS "volunteers";
CREATE TABLE "volunteers" ("id" integer primary key autoincrement not null, "user_id" integer not null, "expertise_subjects" text not null, "grade_levels" varchar not null, "availability" varchar not null, "lesson_format" varchar not null, "years_experience" integer, "short_bio" text, "status" varchar not null default 'pending', "created_at" datetime, "updated_at" datetime, "profile_photo_path" varchar, "show_photo_on_website" tinyint(1) not null default '1', "show_on_website" tinyint(1) not null default '1', "degree_details" text, "degree_document_path" varchar, "certificates_document_path" varchar, "awards" text, "teaching_profile_notes" text, foreign key("user_id") references "users"("id") on delete cascade);

INSERT INTO "volunteers" ("id", "user_id", "expertise_subjects", "grade_levels", "availability", "lesson_format", "years_experience", "short_bio", "status", "created_at", "updated_at", "profile_photo_path", "show_photo_on_website", "show_on_website", "degree_details", "degree_document_path", "certificates_document_path", "awards", "teaching_profile_notes") VALUES (1, 4, 'computer science', 'o levels', 'weekends', 'recorded', 12, 'I am a certified o levels  teacher from last 12 years i have been awarded with the title of Pride of Pakistan from government of Pakistan because of my services in the field of education', 'approved', '2026-02-21 05:21:13', '2026-02-26 22:49:11', 'volunteers/photos/qqV2FdhEqKPMHrQeHZp2Q8y638zDtIw29otXFJgT.jpg', 1, 1, NULL, NULL, 'volunteers/docs/e7Fl1CMC0oLPEZvBkWQM4yz7IlqdZj9xOd84zUMP.pdf', NULL, NULL);
INSERT INTO "volunteers" ("id", "user_id", "expertise_subjects", "grade_levels", "availability", "lesson_format", "years_experience", "short_bio", "status", "created_at", "updated_at", "profile_photo_path", "show_photo_on_website", "show_on_website", "degree_details", "degree_document_path", "certificates_document_path", "awards", "teaching_profile_notes") VALUES (2, 5, 'Islamiat', '0 levels', 'weekends', 'recorded', 12, 'I am a 12 year experienced teacher', 'pending', '2026-02-21 12:00:17', '2026-02-21 12:13:02', 'volunteers/photos/pAMIJp73DFqOEuHKbCDCqDzXWUK41X9jpSpzwlfT.jpg', 1, 1, NULL, NULL, NULL, NULL, NULL);

COMMIT;
