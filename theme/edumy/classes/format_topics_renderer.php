<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . "/course/format/topics/classes/output/renderer.php");
use format_topics\output\renderer;
class theme_edumy_format_topics_renderer extends renderer {

  /**
   * Generate the starting container html for a list of sections
   * @return string HTML to output.
   */
  protected function start_section_list() {
      return html_writer::start_tag('div', array('class' => 'ccn_course_content topics'));
  }

  /**
   * Generate the closing container html for a list of sections
   * @return string HTML to output.
   */
  protected function end_section_list() {
      return html_writer::end_tag('div');
  }

  protected function section_header($section, $course, $onsectionpage, $sectionreturn=null) {
      global $PAGE;

      $o = '';
      $currenttext = '';
      $sectionstyle = '';

      if ($section->section != 0) {
          // Only in the non-general sections.
          if (!$section->visible) {
              $sectionstyle = ' hidden';
          }
          if (course_get_format($course)->is_section_current($section)) {
              $sectionstyle = ' current';
          }
      }

      $o.= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
          'class' => 'section main clearfix details'.$sectionstyle, 'role'=>'region',
          'aria-label'=> get_section_name($course, $section)));

          // $o.= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
          //     'class' => 'section main clearfix'.$sectionstyle, 'role'=>'region',
          //     'aria-label'=> get_section_name($course, $section)));

      if (isset($PAGE->theme->settings->topics_format_collapsible) && ($PAGE->theme->settings->topics_format_collapsible == '1')) { //only first expanded
        $o .= '<div id="accordion" class="panel-group cc_tab ccnTopicFirstExp">
                <div class="panel">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <a href="#panel-'.$section->section.'" class="accordion-toggle link" data-toggle="collapse" data-parent="#accordion">'.get_section_name($course, $section).'</a>
                    </h4>
                  </div>
                  <div id="panel-'.$section->section.'" class="panel-collapse collapse">
                    <div class="panel-body">';
      } elseif (isset($PAGE->theme->settings->topics_format_collapsible) && ($PAGE->theme->settings->topics_format_collapsible == '2')) { //all expanded by default
        $o .= '<div id="accordion" class="panel-group cc_tab">
                <div class="panel">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <a href="#panel-'.$section->section.'" class="accordion-toggle link" data-toggle="collapse" data-parent="#accordion">'.get_section_name($course, $section).'</a>
                    </h4>
                  </div>
                  <div id="panel-'.$section->section.'" class="panel-collapse collapse show">
                    <div class="panel-body">';
      } elseif (isset($PAGE->theme->settings->topics_format_collapsible) && ($PAGE->theme->settings->topics_format_collapsible == '3')) { //all expanded & not collapsible
        $o .= '<div id="accordion" class="panel-group cc_tab ccnTopicNoArr">
                <div class="panel">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <a class="accordion-toggle link">'.get_section_name($course, $section).'</a>
                    </h4>
                  </div>
                  <div id="panel-'.$section->section.'" class="panel-collapse collapse show">
                    <div class="panel-body">';
      } else { //Edumy default
        $o .= '<div id="accordion" class="panel-group cc_tab">
                <div class="panel">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <a href="#panel-'.$section->section.'" class="accordion-toggle link collapsed" data-toggle="collapse" data-parent="#accordion">'.get_section_name($course, $section).'</a>
                    </h4>
                  </div>
                  <div id="panel-'.$section->section.'" class="panel-collapse collapse">
                    <div class="panel-body">';
      }

      // Create a span that contains the section title to be used to create the keyboard section move menu.
      $o .= html_writer::tag('span', get_section_name($course, $section), array('class' => 'hidden sectionname'));

      $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
      $o.= html_writer::tag('div', $leftcontent, array('class' => 'left side'));

      $rightcontent = $this->section_right_content($section, $course, $onsectionpage);
      $o.= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
      $o.= html_writer::start_tag('div', array('class' => 'content'));

      // When not on a section page, we display the section titles except the general section if null
      $hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));

      // When on a section page, we only display the general section title, if title is not the default one
      $hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));

      $classes = ' accesshide';
      if ($hasnamenotsecpg || $hasnamesecpg) {
          $classes = '';
      }
      $sectionname = html_writer::tag('span', $this->section_title($section, $course));
      $o.= $this->output->heading($sectionname, 3, 'sectionname' . $classes);

      $o .= $this->section_availability($section);

      $o .= html_writer::start_tag('div', array('class' => 'summary'));
      if ($section->uservisible || $section->visible) {
          // Show summary if section is available or has availability restriction information.
          // Do not show summary if section is hidden but we still display it because of course setting
          // "Hidden sections are shown in collapsed form".
          $o .= $this->format_summary_text($section);
      }
      $o .= html_writer::end_tag('div');

      return $o;
  }

  /**
   * Generate the display of the footer part of a section
   *
   * @return string HTML to output.
   */
  protected function section_footer() {
      $o = html_writer::end_tag('div');
      $o.= '</div></div></div></div>';
      $o.= html_writer::end_tag('li');

      return $o;
  }


  /**
   * Generate a summary of a section for display on the 'course index page'
   *
   * @param stdClass $section The course_section entry from DB
   * @param stdClass $course The course entry from DB
   * @param array    $mods (argument not used)
   * @return string HTML to output.
   */
  protected function section_summary($section, $course, $mods) {
      global $PAGE;


      $classattr = 'section main details clearfix';
      $linkclasses = '';

      // If section is hidden then display grey section link
      if (!$section->visible) {
          $classattr .= ' hidden';
          $linkclasses .= ' dimmed_text';
      } else if (course_get_format($course)->is_section_current($section)) {
          $classattr .= ' current';
      }

      $title = get_section_name($course, $section);
      $title_raw = get_section_name($course, $section);
      $o = '';
      $o .= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
          'class' => $classattr, 'role'=>'region', 'aria-label'=> $title));

      if (isset($PAGE->theme->settings->topics_format_collapsible) && ($PAGE->theme->settings->topics_format_collapsible == '1')) { //only first expanded
        $o .= '<div id="accordion" class="panel-group cc_tab ccnTopicFirstExp">
                <div class="panel">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <a href="#panel-'.$section->section.'" class="accordion-toggle link" data-toggle="collapse" data-parent="#accordion">'.get_section_name($course, $section).'</a>
                    </h4>
                  </div>
                  <div id="panel-'.$section->section.'" class="panel-collapse collapse">
                    <div class="panel-body">';
      } elseif (isset($PAGE->theme->settings->topics_format_collapsible) && ($PAGE->theme->settings->topics_format_collapsible == '2')) { //all expanded by default
        $o .= '<div id="accordion" class="panel-group cc_tab">
                <div class="panel">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <a href="#panel-'.$section->section.'" class="accordion-toggle link" data-toggle="collapse" data-parent="#accordion">'.get_section_name($course, $section).'</a>
                    </h4>
                  </div>
                  <div id="panel-'.$section->section.'" class="panel-collapse collapse show">
                    <div class="panel-body">';
      } elseif (isset($PAGE->theme->settings->topics_format_collapsible) && ($PAGE->theme->settings->topics_format_collapsible == '3')) { //all expanded & not collapsible
        $o .= '<div id="accordion" class="panel-group cc_tab ccnTopicNoArr">
                <div class="panel">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <a class="accordion-toggle link">'.get_section_name($course, $section).'</a>
                    </h4>
                  </div>
                  <div id="panel-'.$section->section.'" class="panel-collapse collapse show">
                    <div class="panel-body">';
      } else { //Edumy default
        $o .= '<div id="accordion" class="panel-group cc_tab">
                <div class="panel">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <a href="#panel-'.$section->section.'" class="accordion-toggle link" data-toggle="collapse" data-parent="#accordion">'.get_section_name($course, $section).'</a>
                    </h4>
                  </div>
                  <div id="panel-'.$section->section.'" class="panel-collapse collapse">
                    <div class="panel-body">';
      }

      $o .= html_writer::tag('div', '', array('class' => 'left side'));
      $o .= html_writer::tag('div', '', array('class' => 'right side'));
      $o .= html_writer::start_tag('div', array('class' => 'content'));

      if ($section->uservisible) {
          $title = html_writer::tag('a', $title,
                  array('href' => course_get_url($course, $section->section), 'class' => $linkclasses));
      }
      // $o .= $this->output->heading($title, 3, 'section-title');
      $o .= $this->output->heading($title, 3, 'sectionname');

      $o .= $this->section_availability($section);
      $o.= html_writer::start_tag('div', array('class' => 'summarytext'));

      if ($section->uservisible || $section->visible) {
          // Show summary if section is available or has availability restriction information.
          // Do not show summary if section is hidden but we still display it because of course setting
          // "Hidden sections are shown in collapsed form".
          $o .= $this->format_summary_text($section);
          $o .= '<a href="'.course_get_url($course, $section->section).'" class="mt10 mb10 btn btn-primary ccn-btn-icon">'.get_string('resourcedisplayopen').' '.$title_raw.' <span class="flaticon-right-arrow-1"></span></a>';
      }
      $o.= html_writer::end_tag('div');
      $o.= $this->section_activity_summary($section, $course, null);

      $o .= html_writer::end_tag('div');
      $o .= '</div></div></div></div>';
      $o .= html_writer::end_tag('li');

      return $o;
  }

  /**
   * Generate a summary of the activites in a section
   *
   * @param stdClass $section The course_section entry from DB
   * @param stdClass $course the course record from DB
   * @param array    $mods (argument not used)
   * @return string HTML to output.
   */
  protected function section_activity_summary($section, $course, $mods) {
      $modinfo = get_fast_modinfo($course);
      if (empty($modinfo->sections[$section->section])) {
          return '';
      }

      // Generate array with count of activities in this section:
      $sectionmods = array();
      $total = 0;
      $complete = 0;
      $cancomplete = isloggedin() && !isguestuser();
      $completioninfo = new completion_info($course);
      foreach ($modinfo->sections[$section->section] as $cmid) {
          $thismod = $modinfo->cms[$cmid];

          if ($thismod->uservisible) {
              if (isset($sectionmods[$thismod->modname])) {
                  $sectionmods[$thismod->modname]['name'] = $thismod->modplural;
                  $sectionmods[$thismod->modname]['count']++;
              } else {
                  $sectionmods[$thismod->modname]['name'] = $thismod->modfullname;
                  $sectionmods[$thismod->modname]['count'] = 1;
              }
              if ($cancomplete && $completioninfo->is_enabled($thismod) != COMPLETION_TRACKING_NONE) {
                  $total++;
                  $completiondata = $completioninfo->get_data($thismod, true);
                  if ($completiondata->completionstate == COMPLETION_COMPLETE ||
                          $completiondata->completionstate == COMPLETION_COMPLETE_PASS) {
                      $complete++;
                  }
              }
          }
      }

      if (empty($sectionmods)) {
          // No sections
          return '';
      }

      // Output section activities summary:
      $o = '';
      $o.= html_writer::start_tag('ul', array('class' => 'section-summary-activities cs_list mb0'));
      foreach ($sectionmods as $mod) {
          $o.= html_writer::start_tag('li', array('class' => 'activity-count '));
          $o.= '<span class="flaticon-graduation-cap"></span> '.$mod['name'].': '.$mod['count'];
          $o.= html_writer::end_tag('li');
      }

      // Output section completion data
      if ($total > 0) {
          $a = new stdClass;
          $a->complete = $complete;
          $a->total = $total;

          $o.= html_writer::start_tag('li', array('class' => 'section-summary-activities '));
          $o.= '<div class="activity-count"><span class="flaticon-like-1"></span> '.get_string('progresstotal', 'completion', $a).'</div>';
          // $o.= html_writer::tag('span', get_string('progresstotal', 'completion', $a), array('class' => 'activity-count'));
          $o.= html_writer::end_tag('li');
      }
      $o.= html_writer::end_tag('ul');

      return $o;
  }


  /**
   * Output the html for a single section page .
   *
   * @param stdClass $course The course entry from DB
   * @param array $sections (argument not used)
   * @param array $mods (argument not used)
   * @param array $modnames (argument not used)
   * @param array $modnamesused (argument not used)
   * @param int $displaysection The section number in the course which is being displayed
   */
  public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
      global $PAGE;

      $modinfo = get_fast_modinfo($course);
      $course = course_get_format($course)->get_course();

      // Can we view the section in question?
      if (!($sectioninfo = $modinfo->get_section_info($displaysection)) || !$sectioninfo->uservisible) {
          // This section doesn't exist or is not available for the user.
          // We actually already check this in course/view.php but just in case exit from this function as well.
          print_error('unknowncoursesection', 'error', course_get_url($course),
              format_string($course->fullname));
      }

      // Copy activity clipboard..
      echo $this->course_activity_clipboard($course, $displaysection);
      $thissection = $modinfo->get_section_info(0);
      if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
          echo $this->start_section_list();
          echo $this->section_header($thissection, $course, true, $displaysection);
          echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
          echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);
          echo $this->section_footer();
          echo $this->end_section_list();
      }

      // Start single-section div
      echo html_writer::start_tag('div', array('class' => 'single-section'));

      // The requested section page.
      $thissection = $modinfo->get_section_info($displaysection);

      // Title with section navigation links.
      $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
      $sectiontitle = '';
      $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation navigationtitle'));
      $sectiontitle .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
      $sectiontitle .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
      // Title attributes
      $classes = 'sectionname';
      if (!$thissection->visible) {
          $classes .= ' dimmed_text';
      }
      $sectionname = html_writer::tag('span', $this->section_title_without_link($thissection, $course));
      $sectiontitle .= $this->output->heading($sectionname, 3, $classes);

      $sectiontitle .= html_writer::end_tag('div');
      echo $sectiontitle;

      // Now the list of sections..
      echo $this->start_section_list();

      echo $this->section_header($thissection, $course, true, $displaysection);
      // Show completion help icon.
      $completioninfo = new completion_info($course);
      echo $completioninfo->display_help_icon();

      echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
      echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
      echo $this->section_footer();
      echo $this->end_section_list();

      // Display section bottom navigation.
      $sectionbottomnav = '';
      $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
      $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'float-left ui_kit_btn'));
      $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'float-right ui_kit_btn'));
      $sectionbottomnav .= html_writer::tag('div', $this->section_nav_selection($course, $sections, $displaysection),
          array('class' => 'mdl-align'));
      $sectionbottomnav .= html_writer::end_tag('div');
      echo $sectionbottomnav;

      // Close single-section div.
      echo html_writer::end_tag('div');
  }

  /**
   * Generate next/previous section links for naviation
   *
   * @param stdClass $course The course entry from DB
   * @param array $sections The course_sections entries from the DB
   * @param int $sectionno The section number in the course which is being displayed
   * @return array associative array with previous and next section link
   */
  protected function get_nav_links($course, $sections, $sectionno) {
      // FIXME: This is really evil and should by using the navigation API.
      $course = course_get_format($course)->get_course();
      $canviewhidden = has_capability('moodle/course:viewhiddensections', context_course::instance($course->id))
          or !$course->hiddensections;

      $links = array('previous' => '', 'next' => '');
      $back = $sectionno - 1;
      while ($back > 0 and empty($links['previous'])) {
          if ($canviewhidden || $sections[$back]->uservisible) {
              $params = array('class'=>'btn btn-secondary');
              if (!$sections[$back]->visible) {
                  $params = array('class' => 'dimmed_text');
              }
              // $previouslink = html_writer::tag('span', $this->output->larrow(), array('class' => 'larrow'));
              $previouslink .= '<span class="flaticon-left-arrow"></span>&nbsp;&nbsp;';
              $previouslink .= get_section_name($course, $sections[$back]);
              $links['previous'] = html_writer::link(course_get_url($course, $back), $previouslink, $params);
          }
          $back--;
      }

      $forward = $sectionno + 1;
      $numsections = course_get_format($course)->get_last_section_number();
      while ($forward <= $numsections and empty($links['next'])) {
          if ($canviewhidden || $sections[$forward]->uservisible) {
              $params = array('class'=>'btn btn-secondary');
              if (!$sections[$forward]->visible) {
                  $params = array('class' => 'dimmed_text');
              }
              $nextlink = get_section_name($course, $sections[$forward]);
              // $nextlink .= html_writer::tag('span', $this->output->rarrow(), array('class' => 'rarrow'));
              $nextlink .= '&nbsp;&nbsp;<span class="flaticon-right-arrow-1"></span>';
              $links['next'] = html_writer::link(course_get_url($course, $forward), $nextlink, $params);
          }
          $forward++;
      }

      return $links;
  }


}
