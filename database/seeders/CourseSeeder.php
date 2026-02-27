<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $course = Course::updateOrCreate(
            ['slug' => 'continuous-learning'],
            [
                'title' => 'Continuous Learning - Becoming a Star Educator',
                'description' => 'Continuous Learning - Becoming a Star Educator is a professional development journey designed for teachers who want to stay relevant, reflective, and future-ready in rapidly changing classrooms. The course helps educators build a disciplined learning mindset, create supportive environments for growth, and strengthen practical habits that improve teaching quality over time. Through five applied modules, participants learn how to diagnose personal skill gaps, develop learning systems, transform feedback into measurable improvement, and consistently innovate lesson experiences for diverse learners. The course bridges mindset and practice by combining reflective frameworks, collaboration strategies, behavior-change methods, and creativity tools that can be implemented immediately in school settings. By the end of the program, teachers are equipped to lead by example, adapt to new educational demands, and sustain long-term excellence in student-centered instruction.',
                'requirements' => 'Proficient in English',
                'passing_score' => 70,
                'is_active' => true,
            ]
        );

        $lessons = [
            [
                'title' => 'Understanding Continuous Learning',
                'content' => <<<'HTML'
<h2>Module 1: Understanding Continuous Learning</h2>
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

<p>Continuous learning is not an extra burden; it is the operating system of high-quality teaching. When teachers intentionally learn, students receive better questions, stronger explanations, clearer feedback, and more engaging classroom experiences.</p>
HTML,
            ],
            [
                'title' => 'Creating a Learning Culture',
                'content' => <<<'HTML'
<h2>Module 2: Creating a Learning Culture</h2>
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

<p>Creating a learning culture is a strategic process. When teachers consistently reinforce inquiry, accountability, and collaboration, school improvement becomes sustainable and student outcomes become more predictable.</p>
HTML,
            ],
            [
                'title' => 'Becoming a Master Learner',
                'content' => <<<'HTML'
<h2>Module 3: Becoming a Master Learner</h2>
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

<p>Becoming a master learner is a long-term professional identity, not a short-term activity. Teachers who combine strategy, practice, systems, and evidence consistently improve faster and lead stronger learning experiences for every student.</p>
HTML,
            ],
            [
                'title' => 'Receptiveness to Learning & Behaviour Modification',
                'content' => <<<'HTML'
<h2>Module 4: Receptiveness to Learning and Behaviour Modification</h2>
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

<p>Receptiveness and behaviour modification turn professional intent into visible classroom impact. Teachers who practice both consistently become more adaptive, more effective, and more capable of leading meaningful change in school communities.</p>
HTML,
            ],
            [
                'title' => 'Mastering Creativity',
                'content' => <<<'HTML'
<h2>Module 5: Mastering Creativity</h2>
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

<p>Mastering creativity is a professional advantage that combines pedagogical rigor with innovation. Teachers who apply creativity strategically produce stronger learning experiences, deeper student thinking, and more resilient classroom engagement over time.</p>
HTML,
            ],
        ];

        $course->lessons()->delete();
        $course->lessons()->createMany(
            collect($lessons)->map(
                fn (array $lesson, int $index): array => [
                    'title' => $lesson['title'],
                    'content' => $lesson['content'],
                    'order' => $index + 1,
                ]
            )->all()
        );
    }
}
