<?php


function self_assessment_display(){
    wp_enqueue_script('assessment-ajax-script');
    $html = '';

    $html .='        <div class="inside_page">
        <div class="inside_page_left"></div>
        <div class="inside_page_middle">
            <p style="font-size:40px; font-weight:bold;">'.pll__('Self-assessment').'</p>
            <p style="font-size:18px;">'.pll__('Know your abilities and possibilities').'</p>
            </div>
            <div class="inside_page_right"></div>
        </div>
        <h1 style="margin-top: 25px" class="post-title">'.pll__('About and aim').'</h1>
        <p style="max-width: 700px; margin:auto; text-align: center; color:#333333; line-height: 24px; font-size:16px;">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet blanditiis cum cupiditate delectus doloremque ducimus ea explicabo minima minus nesciunt nulla numquam placeat quis repellendus sed, totam vero vitae voluptatibus? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad assumenda, blanditiis eaque fugiat iste sint. A alias aperiam consectetur culpa libero molestias mollitia officiis omnis quis!</p>
        <div style="margin-top:25px;" class="section-banner">
            <div>
                <p style="font-size:24px; font-weight:bold;">'.pll__('Take a test').'</p>
            </div>
        </div>
        ';
    $html .= self_assessment_example();
    $html .= self_assessement_result();
    return $html;
}
add_shortcode( 'selfassessment', 'self_assessment_display');

function self_assessment_example(){
    $html = '';




    $html .= '<form id="self-assessement-form" class="lForm assessement-form" action="" method="post" style="margin-top:25px;">
        <div>
        <p style="text-align: center; margin-top:10px;">1.<br>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores, quia, veritatis. Beatae consectetur deleniti dolore eum ex fuga iste iure modi necessitatibus nostrum officiis perferendis, porro praesentium quia quisquam totam.</p>
        <div class="radio-box">
        <div class="radio-label-box"><input type="radio" name="question-1" value="1"><label class="radio-label">Answer 1</label></div>
        <div class="radio-label-box"><input type="radio" name="question-1" value="2"><label class="radio-label">Answer 2</label></div>
        <div class="radio-label-box"><input type="radio" name="question-1" value="3"><label class="radio-label">Answer 3</label></div>
        </div>
        </div>
         <hr style="width:250px;text-align:center;margin:auto; border-top: 1px solid #989898"> 
        <div>
        <p style="text-align: center; margin-top:10px;">2.<br>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores, quia, veritatis. Beatae consectetur deleniti dolore eum ex fuga iste iure modi necessitatibus nostrum officiis perferendis, porro praesentium quia quisquam totam.</p>
        <div class="radio-box">
        <div class="radio-label-box"><input type="radio" name="question-2" value="1"><label class="radio-label">Answer 1</label></div>
        <div class="radio-label-box"><input type="radio" name="question-2" value="2"><label class="radio-label">Answer 2</label></div>
        <div class="radio-label-box"><input type="radio" name="question-2" value="3"><label class="radio-label">Answer 3</label></div>
        </div>
        </div>
        <hr style="width:250px;text-align:center;margin:auto; border-top: 1px solid #989898"> 
        <div>
        <p style="text-align: center; margin-top:10px;">3.<br>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores, quia, veritatis. Beatae consectetur deleniti dolore eum ex fuga iste iure modi necessitatibus nostrum officiis perferendis, porro praesentium quia quisquam totam.</p>
        <div class="radio-box">
        <div class="radio-label-box"><input type="radio" name="question-3" value="1"><label class="radio-label">Answer 1</label></div>
        <div class="radio-label-box"><input type="radio" name="question-3" value="2"><label class="radio-label">Answer 2</label></div>
        <div class="radio-label-box"><input type="radio" name="question-3" value="3"><label class="radio-label">Answer 3</label></div>
        </div>
        </div>
        <hr style="width:250px;text-align:center;margin:auto; border-top: 1px solid #989898"> 
        <input type="button" id="assessement-example" class="lbutton modal-link" data-modal="assessment" onclick="" value="'.pll__('Submit').'"/>
       
        
    </form>';


    return $html;
}
add_shortcode( 'selfassessment-example', 'self_assessment_example');

function self_assessement_result()
{
    $html = '';
    $html .= '  
<div class="modal MC-modal" data-modal="assessment">
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-close close">×</span>
            <p class="modal-title">'.pll__('Your result!!').'</p>
        </div>
        <div class="modal-body">
            <div>
            <p class="assessement_precentage">0%</p>
            <p class="assessement_resut_tip">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequuntur dignissimos dolorem enim expedita, harum ipsam rerum. Atque cumque doloribus est expedita quibusdam. Deleniti deserunt laboriosam nemo numquam perspiciatis quibusdam soluta.</p>
            </div>
        </div>
    </div>
</div>
            
            ';

    return $html;
}


add_action( 'wp_ajax_assessement_check', 'assessement_check');
add_action( 'wp_ajax_nopriv_assessement_check', 'assessement_check');

function assessement_check(){

    $precentage = intval($_POST['question-1']) + intval($_POST['question-2']) + intval($_POST['question-3']);
    echo round($precentage / 9 * 100);
    wp_die();

}
