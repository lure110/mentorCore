<?php

function login_registration_form(){
    //if( ! headers_sent()) return; // waiting for headers to be sent
    if (is_user_logged_in()) {
        $redirect_url = pll_home_url();
        return '<a class="login_button modal-link" href="javascript:void(0)" data-modal="logout">Logout</a>
<div class="modal MC-modal logout-modal" data-modal="logout">
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-close close">×</span>
        </div>
        <div class="modal-body">
        <p>Are you sure you want to logout?</p>
        <div class="logout-buttos">
        <a href="'. wp_logout_url( $redirect_url ) .'" class="wpex-logout"><button class="logout-buttos-yes">Yes</button></a>
        <button class="logout-buttos-no ">No</button>
        </div>
       </div>
       </div>
       </div>

';
    }
    $plugin_img_dir = WP_PLUGIN_URL ."/mentorCore/img/";
    $content = '
    <style>
    .pop_up, #register_mentor, #register_student, .select_register_type, .already_student{
        display: none;
    }
    </style>
    <a href="javascript:void(0)" class="modal-link" data-modal="login">login</a>
    ';
        // login form
    //TODO Forgot password
  $content .= '
<div class="modal MC-modal" data-modal="login">
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-close close">×</span>
            <p class="modal-title" lang="en">'.pll__('Welcome!').'</p>
            <p id="modal-aditional-text" style="color:#333333; display:none;"></p>
        </div>
        <div class="modal-body">
        <div>
                
                <form id="login_form" action="" method="post">
                    <input id="log_username" type="text" placeholder="'.pll__('Username').'">
                    <input id="log_password" type="password" placeholder="'.pll__('Password').'">
                    <p id="log_status"></p>
                    <input id="login_btn" type="button" onclick="try_log_in();" value="'.pll__('Login').'"/>
                    '.wp_nonce_field( 'ajax-login-nonce', 'loginSecurity' ).'
                </form>
                <p class="new_user_question" > <a class="link" href="javascript:void(0)" onclick="">Forgot password?</a></p>
                <p class="new_user_question" > New user? click <a class="link" href="javascript:void(0)" onclick="show_register_selection();">here</a> to create an account</p>
                
                
                <div class="select_register_type">
                    <input class="simple_btn" onclick="show_mentor_reg();" type="button" value="I am a mentor">
                    <input class="simple_btn" onclick="show_student_reg();" type="button" value="I am a correctional staff member">
                </div>
                
                
                <form id="register_mentor" class="lForm" action="" method="post">
                    <input id="reg_fname" style="" type="text" placeholder="'.pll__('First name').'">
                    <input id="reg_lname" style="" type="text" placeholder="'.pll__('Last name').'">
                    <input id="reg_email" type="text" placeholder="'.pll__('Email').'">
                    <input id="reg_organization" type="text" placeholder="'.pll__('Organization').'">
                    <select id="reg_country">
                        <option value="-1" style="display:none;" disabled selected>'.pll__('Country').'</option>
                        <option value="Germany">'.pll__('Germany').'</option>
                        <option value="Italy">'.pll__('Italy').'</option>
                        <option value="Malta">'.pll__('Malta').'</option>
                        <option value="Romania">'.pll__('Romania').'</option>
                        <option value="Turkey">'.pll__('Turkey').'</option>
                        <option value="Portugal">'.pll__('Portugal').'</option>
                        <option value="Other">'.pll__('Other').'</option>
                    </select>
                    <input id="reg_Username" type="text" placeholder="'.pll__('Username').'">
                    <input id="reg_password" type="password" placeholder="'.pll__('Password').'">
                    <input id="reg_cpassword" type="password" placeholder="'.pll__('Confirm password').'">
                    <select id="reg_qualification">
                        <option value="-1" style="display:none;" disabled selected>'.pll__('Line of work').'</option>
                        <option value="Security">'.pll__('Security').'</option>
                        <option value="Medical">'.pll__('Medical').'</option>
                        <option value="Education">'.pll__('Education').'</option>
                        <option value="Psichology">'.pll__('Psichology').'</option>
                        <option value="Administrative">'.pll__('Administrative').'</option>
                        <option value="Other">'.pll__('Other').'</option>
                    </select>
                    <select id="reg_position">
                        <option value="-1" style="display:none;" disabled selected>'.pll__('Position in career').'</option>
                        <option value="Junior">'.pll__('Junior').'</option>
                        <option value="Senior">'.pll__('Senior').'</option>
                        <option value="Management">'.pll__('Management').'</option>
                        <option value="Top management">'.pll__('Top management').'</option>
                        <option value="Other">'.pll__('Other').'</option>
                    </select><br>                    
                    <input id="reg_terms" type="checkbox"><label style="margin-top:10px;" for="reg_terms">'.pll__('Please, tick the box if you agree to our Privacy and Cookies policy').'</label>
                    <p id="reg_status"></p>
                    <br>
                    <input id="login_btn" class="lbutton" type="button" onclick="" value="'.pll__('Register').'"/>
                    '.wp_nonce_field( 'ajax-register-nonce', 'registerSecurity' ).'
                    <p class="already_student" >Already a user? <a class="link" href="javascript:void(0)" onclick="show_login_form();">log in now!</a></p>                    
                </form>
                
                
                <form id="register_student" class="lForm" action="" method="post">
                <input id="reg_fname" style="max-width:195px; margin-right:4px" type="text" placeholder="'.pll__('First name').'">
                    <input id="reg_lname" style="max-width:195px; margin-left:4px" type="text" placeholder="'.pll__('Last name').'">
                    <input id="reg_email" type="text" placeholder="'.pll__('Email').'">
                    <input id="reg_organization" type="text" placeholder="'.pll__('Organization').'">
                    <select id="reg_country">
                        <option value="-1" style="display:none;" disabled selected>'.pll__('Country').'</option>
                        <option value="Germany">'.pll__('Germany').'</option>
                        <option value="Italy">'.pll__('Italy').'</option>
                        <option value="Malta">'.pll__('Malta').'</option>
                        <option value="Romania">'.pll__('Romania').'</option>
                        <option value="Turkey">'.pll__('Turkey').'</option>
                        <option value="Portugal">'.pll__('Portugal').'</option>
                        <option value="Other">'.pll__('Other').'</option>
                    </select>
                    <input id="reg_Username" type="text" placeholder="'.pll__('Username').'">
                    <input id="reg_password" type="password" placeholder="'.pll__('Password').'">
                    <input id="reg_cpassword" type="password" placeholder="'.pll__('Confirm password').'">
                    <label class="form-label">'.pll__('I would evaluate my carrier management competencies as:').'</label><br>
                    <input name="carrier-management" type="radio"><label class="radio-label">'.pll__('poor').'</label>
                    <input name="carrier-management"type="radio" style=" margin-left: 10px;"><label class="radio-label">'.pll__('good').'</label>
                    <input name="carrier-management" type="radio" style=" margin-left: 10px;"><label class="radio-label">'.pll__('I do not know but I would like to find out').'</label><br>
                    <label class="form-label">'.pll__('Did you took any career development courses online before?').'</label><br>
                    <input name="career-development" type="radio"><label class="radio-label">'.pll__('yes').'</label>
                    <input name="career-development" type="radio" style=" margin-left: 10px;" ><labelclass="radio-label" >'.pll__('no').'</label><br>
                    <label class="form-label">'.pll__('What are your expectations concerning this platform and the objectives you want to achieve').'</label>
                    <textarea id="reg_expectations" class="reg_textarea"></textarea>
                    <select id="reg_qualification">
                        <option value="-1" style="display:none;" disabled selected>'.pll__('Line of work').'</option>
                        <option value="Security">'.pll__('Security').'</option>
                        <option value="Medical">'.pll__('Medical').'</option>
                        <option value="Education">'.pll__('Education').'</option>
                        <option value="Psichology">'.pll__('Psichology').'</option>
                        <option value="Administrative">'.pll__('Administrative').'</option>
                        <option value="Other">'.pll__('Other').'</option>
                    </select>
                    <select id="reg_position">
                        <option value="-1" style="display:none;" disabled selected>'.pll__('Position in career').'</option>
                        <option value="Junior">'.pll__('Junior').'</option>
                        <option value="Senior">'.pll__('Senior').'</option>
                        <option value="Management">'.pll__('Management').'</option>
                        <option value="Top management">'.pll__('Top management').'</option>
                        <option value="Other">'.pll__('Other').'</option>
                    </select><br>
                    <input id="reg_terms" type="checkbox"><label style="margin-top:10px; font-weight: normal; font-size:16px" for="reg_terms">'.pll__('Please, tick the box if you agree to our Privacy and Cookies policy').'</label>
                    <p id="reg_status"></p>
                    <br>
                    <input id="login_btn" class="lbutton" type="button" onclick="" value="'.pll__('Register').'"/>
                    '.wp_nonce_field( 'ajax-register-nonce', 'registerSecurity' ).'                    
                </form>
                
                <p class="already_student" >Already a user? <a class="link" href="javascript:void(0)" onclick="show_login_form();">log in now!</a></p>
            </div>
</div>
    </div>
</div>';
    
    
    
    return $content;
}
add_shortcode( 'login_form_popup', 'login_registration_form' );