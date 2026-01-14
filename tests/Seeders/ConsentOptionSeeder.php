<?php

namespace Visualbuilder\FilamentUserConsent\Tests\Seeders;

use Illuminate\Database\Seeder;
use Visualbuilder\FilamentUserConsent\Models\ConsentOption;
use Visualbuilder\FilamentUserConsent\Models\ConsentOptionQuestion;

class ConsentOptionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
      ConsentOption::factory()
          ->createMany([
              [
                  'key'          => 'terms-and-conditions',
                  'version'      => 1,
                  'title'        => 'Terms and conditions',
                  'label'        => 'Click here to accept the terms.',
                  'sort_order'   => 1,
                  'enabled'    => 1,
                  'is_survey'    => false,
                  'published_at' => now(),
                  'text'         => '<p>To receive services from Neurobox you must consent to our data collection and sharing terms:</p>
<ul class="fa-ul">
<li><i class="fa-li fa fa-check-square"></i> I agree to Neurobox collecting personal data to be able to deliver my service as per my order. </li>
<li><i class="fa-li fa fa-check-square"></i> I agree to sharing my details &amp; outputs of services with my designated coach.</li>
<li><i class="fa-li fa fa-check-square"></i> I agree to any digitally delivered sessions being recorded for internal quality control, training, &amp; monitoring purposes.</li>
<li><i class="fa-li fa fa-check-square"></i> I have read the <a href="https://neurobox.co.uk/privacy-policy/" title="Neurobox Privacy Policy" target="_blank" rel="noopener noreferrer">Neurobox Privacy Policy</a></li>
</ul>',
                  'is_mandatory' => true,
                  'is_current'   => true,
                  'additional_info' => true,
                  'additional_info_title' => "Contract additional info",
                  'fields' => [
                      [
                          "name" => "name",
                          "type" => "text",
                          "label" => "Emergency Contact Name",
                          "rules" => "",
                          "options" => "",
                          "required" => true,
                          "column_span" => 1
                      ],
                      [
                          "name" => "position",
                          "type" => "email",
                          "label" => "Emergency Contact email",
                          "rules" => "",
                          "options" => "",
                          "required" => false,
                          "column_span" => 1
                      ],
                      [
                          "name" => "phone",
                          "type" => "number",
                          "label" => "Emergency Contact Telephone",
                          "rules" => "",
                          "options" => "",
                          "required" => true,
                          "column_span" => 1
                      ],
                      [
                          "name" => "addres",
                          "type" => "textarea",
                          "label" => "Address",
                          "rules" => "",
                          "options" => "",
                          "required" => true,
                          "column_span" => 1
                      ],
                      [
                          "name" => "language",
                          "type" => "select",
                          "label" => "Language",
                          "rules" => "",
                          "options" => "en,fr,tk",
                          "required" => true,
                          "column_span" => 1
                      ],
                      [
                          "name" => "gender",
                          "type" => "radio",
                          "label" => "Gender",
                          "rules" => "",
                          "options" => "Male,Female,Others",
                          "required" => true,
                          "column_span" => 1
                      ],
                      [
                          "name" => "dob",
                          "type" => "date",
                          "label" => "Date of Birth",
                          "rules" => "",
                          "options" => "",
                          "required" => true,
                          "column_span" => 1
                      ],
                  ],
                  'models'       => [
                      [
                          'Visualbuilder\FilamentUserConsent\Tests\Models\User' => 'User'
                      ]
                  ]
              ],
              [
                  'key'          => 'data-sharing',
                  'version'      => 1,
                  'title'        => 'Workplace Needs Assessment - Consent to Share',
                  'label'        => 'I consent to sharing the report, as sent to me, with my employer',
                  'sort_order'   => 2,
                  'enabled'    => 1,
                  'is_survey'    => false,
                  'published_at' => now(),
                  'text'         => '<p>Your workplace needs assessment enables your assessor to gain an understanding of your role, difficulties, and strengths so that they can make suggestions for adjustments that may benefit you. Suggestions may cover areas such as your working environment, working practices, assistive technology, and support.</p>
<p>These suggestions, the rationale behind them, and how they may be of benefit to you, will be presented as a written report to your employer so that they can consider them and implement them as they deem practicable.</p>
<p>Before we can submit your report to your employer, we require your consent to do so. Please tick the box below, to provide your consent.</p>',
                  'is_mandatory' => false,
                  'is_current'   => true,
                  'additional_info' => false,
                  'fields' => [],
                  'models'       => [
                      [
                          'Visualbuilder\FilamentUserConsent\Tests\Models\User' => 'User'
                      ]
                  ]
              ],
              [
                  'key'          => 'coaching-contract',
                  'version'      => 1,
                  'title'        => 'Coaching Contract',
                  'label'        => 'Click here to accept the coaching contract terms',
                  'sort_order'   => 3,
                  'enabled'    => 1,
                  'is_survey'    => false,
                  'published_at' => now(),
                  'text'         => '<p>We have discussed and agreed the following:</p>
<div>The coach will provide {{ total_hours_coaching }} hours of coaching over approximately {{ expected_months }} months.</div>
<p>The purpose of workplace strategy coaching is to enable the client to explore and implement strategies to enhance their effectiveness and wellbeing at work in response to their specific challenges, strengths and situation. These strategies are designed to complement any reasonable adjustments already in place. Specific topics that may be explored include communication and presentations, time management, organisation, project planning, self-advocacy, effective reading and writing techniques, stress management and developing confidence. </p>
<p>
  <span style="text-decoration:underline;">Procedure </span>
</p>
<p>Coaching sessions will take place remotely/face-to-face. The time and duration of the coaching sessions will be agreed by the client and the coach.
  <strong>If a session concludes early at the request of the client, the remaining time will be charged for</strong>.</p>
<p><strong>Late arrival:</strong>&nbsp; on the day of the session, the coach will wait for the client to arrive for a maximum of 15 minutes. If the client has not arrived for the session for this point, it will be terminated and the session may be charged for.</p>
<p><strong>Cancellation policy:</strong>&nbsp;it is the client\'s responsibility to notify the coach if unable to attend a session. </p>
<p>If the client cancels a remote session <strong>less than one working day before the session is scheduled, the session will be charged for</strong>.
<p>If the client cancels a face-to-face session <strong>less than two working days before the session is scheduled, the session will be charged for.</strong></p>
<h3>
  <span style="text-decoration:underline;">Confidentiality </span>
</h3>
<p>The coaching relationship and all information that the client shares with the coach as part of this relationship will remain confidential. The coach agrees not to disclose any information pertaining to the client without the client’s written consent. The coach will not use the client’s name as a reference without the client’s consent. </p>
<p>It is accepted practice that topics arising during coaching sessions may be anonymously shared with other coaching professionals for the purpose of training, supervision, mentoring, evaluation and professional development. </p>
<p>No details that could identify the client will be shared.</p>
<p>In circumstances where the coach is bound by law to disclose information, or the coach reasonably believes there to be an imminent or likely risk of danger or harm to others, the coach is not bound by the above statement.</p>
<p>With the client’s permission, the coach will contact a nominated person in the event of an emergency or where there is concern for the client’s mental wellbeing. </p>
<h3>
  <span style="text-decoration:underline;">Nominated Person Details </span>
</h3>
<p>I hereby give consent for my coach to contact the person below in the event of an emergency, or if there is concern for my mental wellbeing:</p>
<p>Name: </p>
<p>Position: </p>
<p>Telephone Number: </p>
<p>
  <span style="text-decoration:underline;">Client’s Responsibilities </span>
</p>
<ul>
  <li>To attend coaching sessions as agreed </li>
  <li>To select topics for discussion </li>
  <li>To set and pursue meaningful goals </li>
</ul>
<p>
  <span style="text-decoration:underline;">Coach’s Responsibilities </span>
</p>
<ul>
  <li>To manage the coaching process (including timekeeping) </li>
  <li>To maintain confidentiality (except in the circumstances outlined above) </li>
  <li>To undertake regular reflection on their coaching practice  </li>
</ul>
<p>Date:
</p>',
                  'is_mandatory' => true,
                  'is_current'   => true,
                  'additional_info' => true,
                  'additional_info_title' => "Contract additional info",
                  'fields' => [
                      [
                          "name" => "name",
                          "type" => "text",
                          "label" => "Emergency Contact Name",
                          "rules" => "",
                          "options" => "",
                          "required" => true,
                          "column_span" => 1
                      ],
                      [
                          "name" => "position",
                          "type" => "text",
                          "label" => "Emergency Contact Position",
                          "rules" => "",
                          "options" => "",
                          "required" => false,
                          "column_span" => 1
                      ],
                      [
                          "name" => "phone",
                          "type" => "text",
                          "label" => "Emergency Contact Telephone",
                          "rules" => "",
                          "options" => "",
                          "required" => true,
                          "column_span" => 1
                      ]
                  ],
                  'models'       => [
                      [
                          'Visualbuilder\FilamentUserConsent\Tests\Models\User' => 'User'
                      ]
                  ]
              ],
              [
                  'key'          => 'health-partners-coaching-contract',
                  'version'      => 1,
                  'title'        => 'Health Partners Coaching Contract',
                  'label'        => 'Click here to accept the Health Partners coaching contract terms',
                  'sort_order'   => 3,
                  'enabled'    => 1,
                  'is_survey'    => false,
                  'published_at' => now(),
                  'text'         => '
<p>
    <img src="/media/logos/health-partners.png" alt="Health Partners Logo" style="display: block; margin-left: auto; margin-right: 0; height: 60px">
</p>
<p>Health Partners, the OH Service, provide an independent, confidential occupational health service to your organisation. We will only provide information to your organisation and other medical professionals with your consent. By signing this form, you confirm that;</p>
<ol>
  <li>The Specialist has counselled you as to the purpose of this assessment.</li>
  <li>You consent to the assessing specialist releasing information to the clinical team at the OH Service.</li>
  <li>You consent to the OH Service releasing a report containing only relevant clinical information, in strictest confidence, to designated individuals within your organisation who are responsible for your case.</li>
  <li>You confirm that you have been offered the choice to see the report. Please tick your preferred choice. If you do not specify a choice, we will automatically send you a copy at the same time as we release it to your employer.</li>
</ol>
<p>☐ I do not wish to have a copy of the report provided to me.</p>
<p>☐ I wish to have a copy sent to me at the same time as my employer.</p>
<p>☐ I wish to have a copy sent to me 2 working days before it is sent to my employer.&nbsp;</p>
<p>My email is:</p>
<ol start="5">
  <li>You confirm that you have been offered the choice to send a copy of the report to your GP. Please tick your preferred choice</li>
</ol>
<p>☐ I do not want my GP to receive a copy</p>
<p>☐ I would like my GP to receive a copy, his/her details and address is:-</p>
<ol start="6">
  <li>You consent to the OH Service managing and maintaining your medical records in compliance with all ethical requirements and data protection legislation.</li>
  <li>You consent to us possibly auditing your file to ensure that we provide a quality service to all parties</li>
</ol>',
                  'is_mandatory' => true,
                  'is_current'   => true,
                  'additional_info' => false,
                  'fields' => [],
                  'models'       => [
                      [
                          'Visualbuilder\FilamentUserConsent\Tests\Models\User' => 'User'
                      ]
                  ]
              ],
          ]);

      // Add survey questions to consent options
      $termsConsent = ConsentOption::where('key', 'terms-and-conditions')->firstOrFail();

      // Text input question
      ConsentOptionQuestion::create([
          'consent_option_id' => $termsConsent->id,
          'component' => 'text',
          'name' => 'referral_source',
          'label' => 'How did you hear about us?',
          'required' => true,
          'sort' => 1,
      ]);

      // Select question with options
      $selectQuestion = ConsentOptionQuestion::create([
          'consent_option_id' => $termsConsent->id,
          'component' => 'select',
          'name' => 'preferred_contact',
          'label' => 'Preferred contact method',
          'required' => true,
          'sort' => 2,
      ]);

      $selectQuestion->options()->createMany([
          ['value' => 'email', 'text' => 'Email', 'sort' => 1],
          ['value' => 'phone', 'text' => 'Phone', 'sort' => 2],
          ['value' => 'sms', 'text' => 'SMS', 'sort' => 3],
      ]);

      // Radio question with options
      $radioQuestion = ConsentOptionQuestion::create([
          'consent_option_id' => $termsConsent->id,
          'component' => 'radio',
          'name' => 'service_type',
          'label' => 'Which service are you interested in?',
          'required' => true,
          'sort' => 3,
      ]);

      $radioQuestion->options()->createMany([
          ['value' => 'coaching', 'text' => 'Coaching', 'sort' => 1],
          ['value' => 'assessment', 'text' => 'Assessment', 'sort' => 2],
          ['value' => 'both', 'text' => 'Both', 'sort' => 3],
      ]);

      // Textarea question
      ConsentOptionQuestion::create([
          'consent_option_id' => $termsConsent->id,
          'component' => 'textarea',
          'name' => 'additional_notes',
          'label' => 'Any additional notes or requirements',
          'required' => false,
          'sort' => 4,
      ]);

      // Email question (required)
      ConsentOptionQuestion::create([
          'consent_option_id' => $termsConsent->id,
          'component' => 'email',
          'name' => 'contact_email',
          'label' => 'Contact email address',
          'required' => true,
          'sort' => 5,
      ]);

      // Date question (required)
      ConsentOptionQuestion::create([
          'consent_option_id' => $termsConsent->id,
          'component' => 'date',
          'name' => 'preferred_start_date',
          'label' => 'Preferred service start date',
          'required' => true,
          'sort' => 6,
      ]);


      $coachingConsent = ConsentOption::where('key', 'coaching-contract')->firstOrFail();

      // Number question (required)
      ConsentOptionQuestion::create([
          'consent_option_id' => $coachingConsent->id,
          'component' => 'number',
          'name' => 'sessions_per_month',
          'label' => 'Preferred number of sessions per month',
          'required' => true,
          'sort' => 1,
      ]);

      // Likert scale question (required)
      $likertQuestion = ConsentOptionQuestion::create([
          'consent_option_id' => $coachingConsent->id,
          'component' => 'likert',
          'name' => 'communication_comfort',
          'label' => 'How comfortable are you with discussing personal challenges?',
          'required' => true,
          'sort' => 2,
      ]);

      $likertQuestion->options()->createMany([
          ['value' => '1', 'text' => 'Not comfortable', 'sort' => 1],
          ['value' => '2', 'text' => 'Slightly comfortable', 'sort' => 2],
          ['value' => '3', 'text' => 'Moderately comfortable', 'sort' => 3],
          ['value' => '4', 'text' => 'Very comfortable', 'sort' => 4],
          ['value' => '5', 'text' => 'Extremely comfortable', 'sort' => 5],
      ]);

      // Checkbox question (required)
      ConsentOptionQuestion::create([
          'consent_option_id' => $coachingConsent->id,
          'component' => 'check',
          'name' => 'agree_to_terms',
          'label' => 'I agree to the coaching terms and conditions',
          'required' => true,
          'sort' => 3,
      ]);

      // DateTime question (required)
      ConsentOptionQuestion::create([
          'consent_option_id' => $coachingConsent->id,
          'component' => 'datetime',
          'name' => 'first_session_datetime',
          'label' => 'Preferred date and time for first session',
          'required' => true,
          'sort' => 4,
      ]);

    //This code is to automatically approve consents for a user, to save time when manually testing.

    //        $user          = EndUser::first();
    //        $admin         = Admin::first();
    //        $consentOption = ConsentOption::findOrFail(1);
    //
    //        if($user){
    //            $user->consents()
    //                ->save($consentOption, [
    //                    'accepted' => true,
    //                    'key'      => $consentOption->key
    //                ]);
    //        }
    //
    //       if($admin){
    //           $admin->consents()->save($consentOption, [
    //               'accepted' => true,
    //               'key'      => $consentOption->key
    //           ]);
    //       }
  }
}
