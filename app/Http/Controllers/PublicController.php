<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Response\PublicResponse;
use Litepie\Theme\ThemeAndViews;
use Litepie\User\Traits\RoutesAndGuards;
use Illuminate\Support\Facades\DB;
use App\FrontSection;
use App\Testimonial;
use App\Services;
use App\QuickForm;
use App\TeamMember;
use App\Products;
use App\Productcategories;
use App\Location;
use App\Otherforms;
use App\Faqcategories;
use App\Faq;
use App\Review;
use App\Submission;
use App\Bannersection;
use App\Client;
use App\Blog;
use App\Whybannerdrugs;
use App\Blogcategories;
use Litepie\Menu\Models\Menu;
use Illuminate\Support\Facades\Hash;
class PublicController extends Controller
{
    use ThemeAndViews, RoutesAndGuards;
    /**
     * Initialize public controller.
     *
     * @return null
     */
    
    public function __construct()
    {
        $this->response = app(PublicResponse::class);
        $this->setTheme('public');
        $this->baseurl = "https://demo.owengraffix.com/frontlinebargains/";
    }
    public function home()
    {
        
        $page                  = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('home');
        if($page->status == 0)
        {
            return redirect('404');
        }
        $sections                     = FrontSection::where('page_id',$page->id)->orderBy('order_by','asc')->get();
        $product_cat                  = Productcategories::where('status','published')->where('home_features','yes')->get();
        $products_all                 = Products::where('status','published')->get();
        $products_hotitem             = Products::where('status','published')->where('home_hotitem','yes')->get();
        $poster                       = Whybannerdrugs::where('status','published')->get();
        
        foreach($product_cat as $keys=>$vals){
            $product_ac_cat[$vals->slug] = Products::where('status','Published')->where('category','like','%'.$vals->slug.'%')->get();
        }
        $htmlfilters                     = $this->pagesections($page->id);
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('home')
            ->data(compact('page','htmlfilters','product_ac_cat','product_cat','products_all','products_hotitem','poster'))
            ->output();
        
    }
    public function auth()
    {
        $page                  = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('enmy-test-page');
        $sections              = FrontSection::where('page_id',$page->id)->orderBy('order_by','asc')->get();
        $htmlfilters           = $this->pagesections($page->id);
        $userid         = user_id();
        if($userid){
            return redirect('client');
        }
        return $this->response
        ->setMetaKeyword(strip_tags($page->meta_keyword))
        ->setMetaDescription(strip_tags($page->meta_description))
        ->setMetaTitle(strip_tags($page->meta_title))
        ->layout('home')
        ->view('auth')
        ->data(compact('page','htmlfilters'))
        ->output();
    }
    
    public function ordersubmit()
    {
      
        $page          = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('enorder-success');
        $sections      = $page->sections;
        $htmlfilters   = $this->pagesections($page->id);
        
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('ordersubmit')
            ->data(compact('page', 'htmlfilters'))
            ->output();
    }
    public function allblogsshortocode(){
        
        $data                  = Blog::where('status','publish')->orderBy('order_by','asc')->get();
        $blogcategories        = Blogcategories::where('status','published')->orderBy('order_by','asc')->get(); 
        $html  = '';
        foreach($data as $keys=>$values){ 
            if($values->icon){
                $image = url('/').'/'.$values->icon;
            }
            else{
                $image = theme_asset('/').'/images/placeholder.png';
            }
            if($values->author){
                $authors = "— by".$values->author;
            }
            else
            {
                $authors = '';
            } 
        $date_created = $values->publish_date;
        $desc         = substr($values->info,0,100);    
        $d  = date('d', strtotime($date_created));
        $md = date('M', strtotime($date_created));
            
        $date = $values->publish_date;
            
            $date=date_create($date);
            $date= date_format($date,"d M, Y");
            
            
            $html .= '<div class="cf-sm-6 cf-lg-4 col-xs-6 col-sm-6 col-md-4 posts2-i">
                    <a class="posts-i-img" href="'.url('single-post').'/'.$values->slug.'">
                        <span style="background: url('.$image.')"></span>
                    </a>
                    <time class="posts-i-date" datetime="2017-01-01 12:00">
                    <span>'.$d.'</span> '.$md.'</time>
                    <h3 class="posts-i-ttl"><a href="'.url('single-post').'/'.$values->slug.'">'.$values->name.'</a></h3>
                    <p>'.$desc.'</p>        
                    <a href="'.url('single-post').'/'.$values->slug.'" class="posts-i-more">Read more...</a>
                 </div>'; 
            
        }
        
        return $html;   
    }

    public function blogs(){
        
        $page                  = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('en-blogs');
        if($page->status == 0)
        {
            return redirect('404');
        }
        $sections              = FrontSection::where('page_id',$page->id)->orderBy('order_by','asc')->get();
        $htmlfilters           = $this->pagesections($page->id);

        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('blogs')
            ->data(compact('page','htmlfilters'))
            ->output();
        
    }
    
    public function singlepost($slug){
        
        $page  = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('en-blogs');
        if($page->status == 0)
        {
            return redirect('404');
        }
        $sections              = FrontSection::where('page_id',$page->id)->orderBy('order_by','asc')->get();
        $htmlfilters           = $this->pagesections($page->id);
        
        
        $blogs          = Blog::where('slug',$slug)->get();
        $related_blogs  = Blog::where('slug','!=',$slug)->get();
        
        
        foreach($blogs as $keys=>$values)
        {
            $meta_title       =  $values->meta_title;
            $meta_keyword     =  $values->meta_keyword;
            $meta_description =  $values->meta_description; 
            $statussinglepage =  $values->status;  
        }
        if($statussinglepage == "Draft"){
            return redirect('404');
        }
        
        return $this->response
            ->setMetaKeyword(strip_tags($meta_keyword))
            ->setMetaDescription(strip_tags($meta_description))
            ->setMetaTitle(strip_tags($meta_title))
            ->layout('home')
            ->view('singleblog')
            ->data(compact('page','htmlfilters','blogs','related_blogs'))
            ->output();

    }
    public function services()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('services');
        if($page->status == 0)
        {
            return redirect('404');
        }
        
        
        $htmlcontent           = $this->pagesections($page->id);
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('services')
            ->data(compact('page','htmlcontent'))
            ->output();
    }
    public function singleservices($servicesslug)
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('ensingle-services');
        if($page->status == 0)
        {
            return redirect('404');
        }
        $htmlcontent            = $this->pagesections($page->id,$servicesslug);
        $htmlfilters            = $htmlcontent; 
        $services_meta          = Services::where('slug',$servicesslug)->get();
        
        foreach($services_meta as $keys=>$values)
        {
            $meta_title       =  $values->meta_title;
            $meta_keyword     =  $values->meta_keyword;
            $meta_description =  $values->meta_description; 
            $statussinglepage =  $values->status;  
        }
        if($statussinglepage == "Draft"){
            return redirect('404');
        }
        return $this->response
            ->setMetaKeyword(strip_tags($meta_keyword))
            ->setMetaDescription(strip_tags($meta_description))
            ->setMetaTitle(strip_tags($meta_title))
            ->layout('home')
            ->view('single-service')
            ->data(compact('page','htmlfilters'))
            ->output();
    }
    public function locations()
    {
        $page           = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('enlocations');
        if($page->status == 0)
        {
            return redirect('404');
        }
        $sections       = FrontSection::where('page_id',$page->id)->orderBy('order_by','asc')->get();
        $htmlcontent    = $this->pagesections($page->id);
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('locations')
            ->data(compact('page','htmlcontent'))
            ->output();
    }
   
    public function singlelocation($locationslug)
    {
        
        $page  = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('ensingle-location');
        if($page->status == 0)
        {
            return redirect('404');
        }
        $servicesslug          = "";
        $htmlcontent           = $this->pagesections($page->id,$servicesslug="",$locationslug);
        $locationdata          = Location::where('slug',$locationslug)->get();
        
        foreach($locationdata as $keys=>$values)
        {
            $meta_title       =  $values->meta_title;
            $meta_keyword     =  $values->meta_keyword;
            $meta_description =  $values->meta_description; 
            $statussinglepage =  $values->status;    
        }
        if($statussinglepage == "Draft"){
            return redirect('404');
        }
        
        return $this->response
            ->setMetaKeyword(strip_tags($meta_keyword))
            ->setMetaDescription(strip_tags($meta_description))
            ->setMetaTitle(strip_tags($meta_title))
            ->layout('home')
            ->view('single-location')
            ->data(compact('page','data','htmlcontent'))
            ->output();
    }
    
    public function contact()
    {
        $page                                 = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('contact');
        if($page->status == 0)
        {
            return redirect('404');
        }
        $htmlcontent                          = $this->pagesections($page->id,$servicesslug="",$locationslug="");
        $htmlfilters                          = $htmlcontent; 
        return $this->response
               ->setMetaKeyword(strip_tags($page->meta_keyword))
               ->setMetaDescription(strip_tags($page->meta_description))
               ->setMetaTitle(strip_tags($page->meta_title))
               ->layout('home')
               ->view('contact')
               ->data(compact('page','htmlfilters'))
               ->output();
    }
       
    public function workwithus()
    {
        $page                                 = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('enwork-with-us');
        if($page->status == 0)
        {
            return redirect('404');
        }
        $htmlcontent                          = $this->pagesections($page->id,$servicesslug="",$locationslug="");
        
        $htmlfilters                          = $htmlcontent; 
        
        return $this->response
               ->setMetaKeyword(strip_tags($page->meta_keyword))
               ->setMetaDescription(strip_tags($page->meta_description))
               ->setMetaTitle(strip_tags($page->meta_title))
               ->layout('home')
               ->view('workwithus')
               ->data(compact('page','htmlfilters'))
               ->output();
    }
    
    
    
    public function about()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('enabout');
        if($page->status == 0)
        {
            return redirect('404');
        }
      $htmlcontent           = $this->pagesections($page->id,$servicesslug="",$locationslug="");
      $htmlfilters =  $htmlcontent;     
      return $this->response
        ->setMetaKeyword(strip_tags($page->meta_keyword))
        ->setMetaDescription(strip_tags($page->meta_description))
        ->setMetaTitle(strip_tags($page->meta_title))
        ->layout('home')
        ->view('about')
        ->data(compact('page','htmlfilters'))
        ->output(); 
    }
    
    public function teams()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('enteam');

        $htmlcontent           = $this->pagesections($page->id,$servicesslug="",$locationslug="");
        $htmlfilters           = $htmlcontent; 
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('team')
            ->data(compact('page','htmlfilters'))
            ->output();
    }
    
    public function elements(){
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('enemployees');
        $htmlcontent           = $this->pagesections($page->id,$servicesslug="",$locationslug="");
        $htmlfilters           =  $htmlcontent; 
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('element')
            ->data(compact('page','htmlfilters'))
            ->output();
    }   

    public function products(){

        $page                     = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('enproduct');
        $htmlcontent              = $this->pagesections($page->id,$servicesslug="",$locationslug="");
        
        $products_all             = Products::where('status','published')->get();
        $product_cat              = Productcategories::where('status','published')->get();
    
        
//        foreach($product_cat as $keys=>$vals){
//            $product_ac_cat[$vals->slug] = Products::where('status','Published')->where('category','like','%'.$vals->slug.'%')->get();
//        }
//        
        
        $htmlfilters           =  $htmlcontent; 
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('products')
            ->data(compact('page','htmlfilters','products_all','product_cat'))
            ->output();
        
    }
    
    public function products_cat($slug){
        
        $page                     = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('enproduct');
        $htmlcontent              = $this->pagesections($page->id,$servicesslug="",$locationslug="");
        $product_cat              = Productcategories::where('status','published')->get();
        $product_Cat_title        = Productcategories::where('slug',$slug)->get();

        foreach($product_Cat_title as $ptkeys=>$ptval){
            $cate_title = $ptval->title;
        }
        
        $cat_slug                  = $slug; 
        $products_all              = Products::where('status','Published')->where('category','like','%'.$slug.'%')->get();
        $htmlfilters               = $htmlcontent; 
        
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('products')
            ->data(compact('page','htmlfilters','products_all','product_cat','cate_title','cat_slug'))
            ->output();
        
    }

    public function singleproducts($slug){
        
        $page                  =  app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('enproduct');
        $htmlcontent           =  $this->pagesections($page->id,$servicesslug="",$locationslug="");
        $htmlfilters           =  $htmlcontent; 
        $all_products          =  Products::where('slug',$slug)->get();
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('single-product')
            ->data(compact('page','htmlfilters','all_products'))
            ->output();
    }
    
    public function checkout()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('enwhy');
        if($page->status == 0)
        {
            return redirect('404');
        }
        $htmlcontent           = $this->pagesections($page->id,$servicesslug="",$locationslug="");
        $htmlfilters =  $htmlcontent;
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('checkout')
            ->data(compact('page','htmlfilters'))
            ->output();
    }
    
    public function faq()
    {

        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('enfaq');
        if($page->status == 0)
        {
            return redirect('404');
        }
        $htmlcontent           = $this->pagesections($page->id,$servicesslug="",$locationslug="");
        return $this->response
               ->setMetaKeyword(strip_tags($page->meta_keyword))
               ->setMetaDescription(strip_tags($page->meta_description))
               ->setMetaTitle(strip_tags($page->meta_title))
               ->layout('home')
               ->view('faq')
               ->data(compact('page','htmlcontent'))
               ->output();
    }
    public function team($teammembername){
        $page                     = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('ensingle-team');
        if($page->status == 0)
        {
            return redirect('404');
        }
        $htmlcontent              = $this->pagesections($page->id,$servicesslug="",$locationslug="",$teammembername);
        $htmlfilters              = $htmlcontent;
        $teammember_meta          = TeamMember::where('slug',$teammembername)->get();
        foreach($teammember_meta as $keys=>$values)
        {
            $meta_title       =  $values->meta_title;
            $meta_keyword     =  $values->meta_keyword;
            $meta_description =  $values->meta_description; 
        }
        return $this->response
               ->setMetaKeyword(strip_tags($meta_keyword))
               ->setMetaDescription(strip_tags($meta_description))
               ->setMetaTitle(strip_tags($meta_title))
               ->layout('home')
               ->view('single-team')
               ->data(compact('page','htmlfilters'))
               ->output();
    }
    public function notfound()
    {
        $page        = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('notfound');
        $htmlcontent = $this->pagesections($page->id,$servicesslug="",$locationslug="");

        $htmlfilters =  $htmlcontent; 
        return $this->response
               ->setMetaKeyword(strip_tags($page->meta_keyword))
               ->setMetaDescription(strip_tags($page->meta_description))
               ->setMetaTitle(strip_tags($page->meta_title))
               ->layout('home')
               ->view('404')
               ->data(compact('page','htmlfilters'))
               ->output();
        
    }
    public function privacy()
    {
        $page                  = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('enprivacy');
        if($page->status == 0)
        {
            return redirect('404');
        }
        $htmlcontent           = $this->pagesections($page->id);
        $htmlfilters           = $htmlcontent;
        

        return $this->response
               ->setMetaKeyword(strip_tags($page->meta_keyword))
               ->setMetaDescription(strip_tags($page->meta_description))
               ->setMetaTitle(strip_tags($page->meta_title))
               ->layout('home')
               ->view('privacy')
               ->data(compact('page','htmlfilters'))
               ->output();   
    }
    public function getteaminfo($teammembername)
    {
        $teamMembers = TeamMember::where('slug',$teammembername)->get();
        
        foreach($teamMembers as $tmkey=>$tmval)
        {
            
            if($tmval->photo)
            {
                 $img_url = url('/').'/'.$tmval->photo;
            }
            else
            {
                $img_url = theme_asset('images/placeholder.png');
            }
            $html ='<div class="container-fluid">
                    <div class="row">
                        <div class="col-12 col-lg-8">
                        <div class="section-headline">
                        <h3>'.$tmval->name.'</h3>
                        </div>
                        <p>
                        '.$tmval->bio.'
                        </p> 
                        </div>
               
        
        
          <div class="col-12 col-lg-4">
            <img src="'.$img_url.'" alt="" class="img-fluid w-100">
          </div>
          </div>
          </div>
          ';  
            
      
            
            
         if($tmval->qanda):   
            $html .='<div class="" id="q-and-a">
            <div class="container-fluid">
              <div class="row">
                  <div class="col-12">
                    <div class="section-headline">
                      <h3>Q &amp; A</h3>
                    </div>
                  </div>
                </div>
                <div class="row">
                    '.$tmval->qanda.'
                </div>
              </div>
            </div>';
        endif;  
            
            
            
        }
        return $html;
        
    }
    public function getallfaqs()
    {
        
        $faq_categories = Faqcategories::where('status','publish')->orderBy('order_by','asc')->get();
        foreach($faq_categories as $fkeys=>$fvalues)
        {
             $faqs_by_cat[] = Faq::select('question','answer')->where('category',$fvalues->slug)->orderBy('order_by','asc')->get(); 
             $faqs_title[]  = $fvalues->title;
        }
        $x = 0;

        $faqsallhtml = "<div class='container'>
                         <div class='accordion' id='accordionExample'>";
        foreach($faqs_by_cat as $fkeys =>$fvalues)
        { 
            if(count($fvalues) > 0)
            {
                $faqsallhtml .= '<h3>'.$faqs_title[$fkeys].'</h3>';   
                foreach($fvalues as $inval)
                {
                if($x!=0)      
                {  
                    $active ="";
                    $actives = "";
                }
                else
                {
                    $active = "show";
                    $actives = "active";
                }

                if($x == 0)
                {
                    $collapsed = "collapsed";
                }
                else
                {
                    $collapsed = "";
                }
                    
              
                    
                $faqsallhtml .= '<p data-accordion-num="'.$x.'" class="accordion-tab-mob '.$active.' '.$actives.'" data-accordion="#accordion-tab-'.$x.'">'.$inval->question.'</p>
                <div class="accordion-tab" id="accordion-tab-'.$x.'">
                    <div class="accordion-inner">
                       '.$inval->answer.' 
                    </div>
                </div>';  
                $x++;
                    
                    
                    
                    
                }
            }
        }
        $faqsallhtml .= '</div></div>';
        return $faqsallhtml; 
        
        
    }
    public function testimonial()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('testimonials');
        if($page->status == 0)
        {
            return redirect('404');
        }
        $htmlcontent           = $this->pagesections($page->id,$servicesslug="",$locationslug="");
        return $this->response
               ->setMetaKeyword(strip_tags($page->meta_keyword))
               ->setMetaDescription(strip_tags($page->meta_description))
               ->setMetaTitle(strip_tags($page->meta_title))
               ->layout('home')
               ->view('faq')
               ->data(compact('page','htmlcontent'))
               ->output();
        
    }
    public function getformbyslug($slug)
    {
        $quickForm      = QuickForm::where('slug',$slug)->get();
        $quickForm_html = '';
        foreach($quickForm as $tkey=>$tval)
        {
            $quickForm_html .= $tval->form_field;
        }
        return $quickForm_html;
    } 
    
    public function bannersections()
    {
        
        $bannersection     = Bannersection::where('status','Published')->orderBy('order_by', 'asc')->get();
        $bannersectionhtml ='';
        $bannersectionhtml .= '<ul class="slides">';
        foreach($bannersection as $bkey=>$bval)
        {
            
            
            if($bval->icons_image)
            {
                 $img_url = url('/').'/'.$bval->icons_image;
            }
            else
            {
                $img_url = theme_asset('images/placeholder.png');
            } 
           
            if($bkey == 0)
            {
               $activec = "active";
            }
            else
            {
                $activec = "";
            }
            $bannersectionhtml .='<li>
            
         
                    
                    
                    <div class="fr-slider-cont">
                         <h3>'.$bval->name.'</h3>
                         <p>'.$bval->content.'</p>
                        <p class="fr-slider-more-wrap">
                            <a class="fr-slider-more" href="'.$bval->url.'">View collection</a>
                        </p>
                    </div>
             
                    <div class="wrap-image">
                            <img src="'.$img_url.'" alt="">
                    </div>
                 
                </li>'; 
        }
        $bannersectionhtml .= '</ul>';
      
        return $bannersectionhtml;   
    }
    public function gettestimonilas()
    {
        $testimonials = Testimonial::all();
        $testimonailshtml = '<div class="carousel-inner">';
        foreach($testimonials as $tkey=>$tval)
        {
            if($tkey == 0)
            {
               $active = "active";
            }
            else
            {
                $active = "";
            }
            $testimonailshtml .= '
                      <div class="carousel-item '.$active.'">
                        <div class="d-block">
                          <p class="review-text">'.$tval->description.'</p>
                          <p class="review-author">- '.$tval->title.'</p>
                        </div>
                      </div>
               '; 
        }
         $testimonailshtml .= '</div>
            <a class="carousel-control-prev" href="#client-review-carousel" role="button" data-slide="prev">
              <img src="'.theme_asset('img/icons/angle-left-secondary.svg').'" alt="angle left icon" class="angle-left-icon">
              <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#client-review-carousel" role="button" data-slide="next">
              <img src="'.theme_asset('img/icons/angle-left-secondary.svg').'" alt="angle right icon" class="angle-right-icon">
              <span class="sr-only">Next</span>
            </a>';
        
       
        return $testimonailshtml;
    }
    public function gettestimonilasall()
    {
        $testimonials = Testimonial::where('status','published')->orderBy('order_by','asc')->get();
        $testimonailshtml = '<div class="container-fluid">
                                <div class="row">';
        foreach($testimonials as $tkey=>$tval)
        {
            if($tkey == 0)
            {
               $active = "active";
            }
            else
            {
                $active = "";
            }
            $testimonailshtml .= '
                      <div class="col-xl-6 col-md-6 col-12">
                        <div class="p_testimonial_item">
                          <div class="wrap-content">
                            <p class="review-text">'.$tval->description.'</p>
                            <h4>- '.$tval->title.'</h4>
                          </div>
                        </div>
                      </div>
               '; 
        }
        
        $testimonailshtml .= '</div></div>';
        return $testimonailshtml;
    }
   
    public function getservices()
    {
        $services = Services::where('status','Published')->get();
        $serviceshtml = '<div class="demo">';
        foreach($services as $tkey=>$tval)
        {
            if($tkey == 0)
            {
               $active = "active";
            }
            else
            {
                $active = "";
            }
            
            if($tval->icons)
            {
                 $img_url = url('/').'/'.$tval->icons;
            }
            else
            {
                $img_url = theme_asset('default/packaging.png');
            }
            
            
            
            
            $serviceshtml .= '<div class="px-1">
                        <div class="card">
                           <a href="'.url('single-services').'/'.$tval->slug.'">
                            <img class="card-img-top card-image" src="'.$img_url.'" alt="Card image cap">
                          </a>
                          <div class="card-body">
                            <h5 class="card-title">'.$tval->title.'
                              <hr class="underline">
                            </h5>
                            <p class="card-text">'.strip_tags(substr($tval->descriptions,0,100)).'...</p>
                            <div class="text-right">
                              <a href="'.url('single-services').'/'.$tval->slug.'" class="d-block ml-auto card-btn">Read More</a>
                            </div>
                          </div>
                        </div>
                      </div>'; 
        }
        $serviceshtml .= '</div>
                <div class="row">
                    <div class="col-12 mt-5 text-center">
                        <a href="'.url('services').'" class="btn btn-primary mx-auto">See All</a> 
                    </div>
                </div>
        ';
        return $serviceshtml;
    }
    
    public function getservices_servicepage()
    {
        $servicespage = Services::where('status','Published')->orderBy('order_by','asc')->get();
        $servicespagehtml = '';
        foreach($servicespage as $tkey=>$tval)
        {
            if($tkey == 0)
            {
               $active = "active";
            }
            else
            {
                $active = "";
            }
            
            if($tval->icons)
            {
                 $img_url = url('/').'/'.$tval->icons;
            }
            else
            {
                $img_url = theme_asset('default/packaging.png');
            }
            
            $servicespagehtml .='            
             <div class="col-12 col-sm-6 col-lg-4">
                <div class="card services-card">
                   <a href="'.url('single-services').'/'.$tval->slug.'">
                    <img class="card-img-top" src="'.$img_url.'" alt="Card image cap">
                   </a>
                  <div class="card-body">
                    <div id="heading'.$tkey.'">
                      <div class="mb-0">
                      <button class="collapsed accordion-heading-button" type="button" data-toggle="collapse" data-target="#collapse'.$tkey.'" aria-expanded="true" aria-controls="collapse'.$tkey.'">
                      <span class="accordion-heading">'.$tval->title.'</span>
                      <img class="accordion-heading-icon d-md-none" src="'.theme_asset('default/angle-up-secondary.svg').'" alt="angle-down">
                      </button>
                      </div>
                    </div>
                    <div id="collapse'.$tkey.'" class="collapse" aria-labelledby="heading'.$tkey.'" data-parent="#services-accordion">
                      <div class="card-text sr-dec">
                        
                          <p class="card-text">'.strip_tags($tval->descriptions).'</p>
                  
                      </div>
                      <div class="d-flex justify-content-end" style="margin-top:10px;">
                         <a href="'.url('single-services').'/'.$tval->slug.'" class="card-btn">Learn More</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>'; 
        }
        return $servicespagehtml;
    }
    
    public function getlocations()
    {
        error_reporting(0);
        $locations     = Location::where('status','Published')->orderBy('order_by','asc')->get();
        $locationshtml = '';
        $collapse = 'collapse';
        foreach($locations as $lkey=>$ltval)
        {
            if($lkey == 0)
            {
               $active = "active";
            }
            else
            {
                $active = "";
            }
            
            if($ltval->image)
            {
                 $img_url = url('/').'/'.$ltval->image;
            }
            else
            {
                $img_url = theme_asset('default/our-stores.png');
            }
            
            $locationshtml .= '<div class="col-12 col-sm-6 col-lg-4">
                  <div class="card store-card">
                    <a href="'.url('single-location').'/'.$ltval->slug.'">
                      <img class="card-img-top" src="'.$img_url.'" alt="Card image cap">
                    </a>
                    <div class="store-card-body">
                      <div id="heading'.$lkey.'">
                        <div class="mb-0">
                          <button class="collapsed accordion-heading-button" type="button" data-toggle="collapse" data-target="#collapse'.$lkey.'" aria-expanded="true" aria-controls="collapse'.$lkey.'">
                          <span class="accordion-heading">'.$ltval->title.'</span> 
                          <img class="accordion-heading-icon d-md-none" src="'.theme_asset('default/angle-up-secondary.svg').'" alt="angle-down">
                         </button>
                        </div>
                      </div>

                      <div id="collapse'.$lkey.'" aria-labelledby="heading'.$lkey.'" data-parent="#locations-accordion" class="custom_collapse"> 
                        <div class="row">
                          <div class="col-12 d-flex mt-3">
                            <img class = "icon" src="'.theme_asset('default/location-icon.svg').'" alt="pin">
                            <p class="store-card-info">'.$ltval->address.'
                            </p>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-12 d-flex">
                          <img class = "icon" src="'.theme_asset('default/small-phone-icon.svg').'" alt="pin">
                          <p class="store-card-info">'.$ltval->mobile.'
                          </p>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-12 d-flex">
                              <img class="icon" src="'.theme_asset('default/clock-icon.svg').'" alt="pin">
                              <div class="w-100 store-card-info">
                                '.$ltval->timing.'
                            </div>
                          </div>
                        </div>
                        <div class="text-right">
                        <a href="'.url('single-location').'/'.$ltval->slug.'" class="card-btn">Learn More</a>
                        </div>
                      </div>
                    </div>      
                  </div> 
                </div>'; 
        }
        return $locationshtml; 
    }
    public function getfaqs($limit)
    {
        error_reporting(0);
        $faqs     = Faq::where('status','publish')->limit($limit)->get();
        $faqshtml = '';
        $collapse = 'collapse';
        foreach($faqs as $fkey=>$ftval)
        {
         $faqshtml .= '<div class="col-12 col-lg-4">
            <p>Q: '.$ftval->question.'</p>
            <div class="underline"></div>
            <p>A: '.$ftval->answer.'</p>
          </div>'; 
        }
        return $faqshtml; 
    }
    
    public function getteammember()
    {
        $teamMembers = TeamMember::where('status','publish')->orderBy('order_by','asc')->get();
        $teamMemberhtml = '';
        foreach($teamMembers as $tmkey=>$tmval)
        {
            if($tmkey == 0)
            {
               $active = "active";
            }
            else
            {
                $active = "";
            }
            
            if($tmval->photo)
            {
                 $img_url = url('/').'/'.$tmval->photo;
            }
            else
            {
                $img_url = theme_asset('images/placeholder.png');
            }
            

            
            $teamMemberhtml .='<div class="col-sm-4 team-i">
                    <p class="team-i-img">
                        <img src="'.$img_url.'" alt="'.$tmval->name.'">
                    </p>
                    <h3 class="team-i-ttl">Harold Augustine</h3>
                    <p class="team-i-post">Director</p>
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua
                    <span class="team-i-margin"></span>
                    
                    <ul class="team-i-social">
                        <li><a href="'.$tmval->social_f.'"><i class="fa fa-facebook-square"></i></a></li>
                        <li><a href="'.$tmval->social_t.'"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="'.$tmval->social_l.'"><i class="fa fa-linkedin-square"></i></a></li>
                    </ul>
                </div>';
            
        }
        return $teamMemberhtml;
    }   
    
    public function getteammembergrid()
    {
        
        $teamMembers = TeamMember::where('status','publish')->orderBy('order_by','asc')->get();
        $teamMemberhtml = '';
        foreach($teamMembers as $tkey=>$tval)
        {
            if($tkey == 0)
            {
               $active = "active";
            }
            else
            {
                $active = "";
            }
            
            if($tval->photo)
            {
                 $img_url = url('/').'/'.$tval->photo;
            }
            else
            {
                 $img_url = theme_asset('images/placeholder.png');
            }
            
            
            $getlocations = DB::table('locations')->where('slug',$tval->locations)->get();
            foreach($getlocations as $loc=>$val)
            {
                $locations =  $val->title;
            }
            
            $teamMemberhtml .='            
             <div class="col-12 col-sm-6 col-lg-4">
                <div class="card services-card">
                   <a href="'.url('team').'/'.$tmval->slug.'">
                    <img class="card-img-top" src="'.$img_url.'" alt="Card image cap">
                   </a>
                  <div class="card-body">
                    <div id="heading'.$tkey.'">
                      <div class="mb-0">
                        <button class="collapsed accordion-heading-button" type="button" data-toggle="collapse" data-target="#collapse'.$tkey.'" aria-expanded="true" aria-controls="collapse'.$tkey.'">
                        <span class="accordion-heading">'.$tval->name.'</span>
                        <img class="accordion-heading-icon d-md-none" src="'.theme_asset('default/angle-up-secondary.svg').'" alt="angle-down">
                        </button>
                      </div>
                    </div>
                    <div id="collapse'.$tkey.'" class="collapse" aria-labelledby="heading'.$tkey.'" data-parent="#services-accordion">
                      <div class="card-text sr-dec">
                        
                       <p class="card-text">Location: Banner Drug of '.$locations.'<br>Title: '.$tval->title.'</p>
                  
                      </div>
                      <div class="d-flex justify-content-end" style="margin-top:10px;">
                         <a href="'.url('team').'/'.$tval->slug.'" class="card-btn">Learn More</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>'; 
        }
        return $teamMemberhtml;  
           
    }
    public function getteammemberbylocation($locations)
    {
        $teamMembers = TeamMember::where('status','publish')->where('locations',$locations)->orderBy('order_by','asc')->get();
        $teamMemberhtml = '';
        foreach($teamMembers as $tmkey=>$tmval)
        {
            if($tmkey == 0)
            {
               $active = "active";
            }
            else
            {
                $active = "";
            }
            
            if($tmval->photo)
            {
                 $img_url = url('/').'/'.$tmval->photo;
            }
            else
            {
                $img_url = theme_asset('images/placeholder.png');
            }
            
            $teamMemberhtml .= '<div class="px-2 '.$tmval->slug.'">
                                <div class="card">
                                  <a href="'.url('team').'/'.$tmval->slug.'">
                                    <div class="wrap-image">
                                        <img class="card-img-top card-image" src="'.$img_url.'" alt="Card image cap">
                                    </div>
                                  </a>
                                  <div class="card-body">

                                    <h5 class="card-title">'.$tmval->name.'</h5>

                                    <hr class="underline">

                                    <p class="card-text">Location: '.$tmval->address.'<br>Title: '.$tmval->title.'</p>

                                    <div class="text-right">
                                      <a href="'.url('team').'/'.$tmval->slug.'" class="card-btn">Learn More</a>
                                    </div>

                                  </div>
                                </div>
                          </div>'; 
        }
        return $teamMemberhtml;
    }
    
 

    public function getsinglestoreinfo($slug){  
        $data = Location::where('slug',$slug)->where('status','Published')->orderBy('order_by','asc')->get(); 
        $singlelocationhtml = '';
        foreach($data as $lkey=>$lval){
            
            if($lval->image)
            {
                 $img_url = url('/').'/'.$lval->image;
            }
            else
            {
                $img_url = theme_asset('default/our-stores.png');
                
            }
            
            $rating_number = '';
            
            for($x=0;$x<$lval->star;$x++)
            {
               $rating_number .= '<img src="'.theme_asset('default/icon-rating-star.svg').'" alt="" class="rating-star">'; 
            }
       
 
    
       $singlelocationhtml .= '<div class="row d-lg-none mb-5">
        <div class="col-12">
        <div class="card">
        <div class="card-body">
        <h5 class="card-title store-info-title">'.$lval->title.'</h5>
        <div class="card-text">
            <div class="row">
                <div class="col-12 d-flex mt-3">
                <img class="icon" src="'.theme_asset('default/location-icon.svg').'" alt="pin">
                <p class="store-card-info">'.$lval->address.'
                </p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 d-flex">
                    <img class = "icon" src="'.theme_asset('default/small-phone-icon.svg').'" alt="pin">
                    <p class="store-card-info">'.$lval->mobile.'
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 d-flex">
                    <img class = "icon" src="'.theme_asset('default/clock-icon.svg').'" alt="pin">
                        <div class="w-100 store-card-info">
                        '.$lval->timing.'
                       </div>
                </div>
            </div>
        </div>
        </div>
        <img class="card-img-bottom" src="'.$img_url.'" alt="Card image bottom">
        </div>
    </div>
</div>
<div class="row d-none d-lg-flex">
    <div class="col-lg-5">
        <h5 class="card-title store-info-title">'.$lval->title.'</h5>
        <div class="google-rating mb-3">
        <div class="rating-stars"> 
            '.$rating_number.'
        </div>
        <span>'.$lval->rating.' Google Rating</span>
        </div>
        <div class="row">
            <div class="col-12 d-flex mt-3">
                <img class = "icon" src="'.theme_asset('default/location-icon.svg').'" alt="pin">
                <p class="store-card-info">'.$lval->address.'
                </p>
            </div>
        </div>
        <div class="row">
        <div class="col-12 d-flex">
        <img class = "icon" src="'.theme_asset('default/small-phone-icon.svg').'" alt="pin">
        <p class="store-card-info">'.$lval->mobile.'
        </p>
        </div>
        </div>
        <div class="row">
        <div class="col-12 d-flex">
        <img class="icon" src="'.theme_asset('default/clock-icon.svg').'" alt="pin">
        <div class="w-100 store-card-info">
          '.$lval->timing.'
        </div>
        </div>
        </div>
    </div>
    <div class="col-lg-7">
        <img class="img-fluid" src="'.$img_url.'" alt="" >
    </div>
</div>';  
            unset($rating_number);
            return $singlelocationhtml;
        }
        
    }
    public function getsingleservicesinfo($slug){  
        $data = Services::where('slug',$slug)->get(); 
        $singleserviceshtml = '';
        foreach($data as $lkey=>$lval){
            
            if($lval->icons)
            {
                 $img_url = url('/').'/'.$lval->icons;
            }
            else
            {
                $img_url = theme_asset('default/our-stores.png');
                
            }
            
            if($lval->print_url)
            {
                $print_url = '<div class="col text-center text-md-left">
                                <a href="'.$lval->print_url.'" class="btn btn-primary  my-3">Print Form</a>
                             </div>';
            }
            else
            {
                $print_url = '';
                
            } 
            
            $rating_number = '';
            
            for($x=0;$x<$lval->star;$x++)
            {
               $rating_number .= '<img src="'.theme_asset('default/icon-rating-star.svg').'" alt="" class="rating-star">'; 
            }
       
 
    
          $singleserviceshtml .= '<div class="container-fluid">
                             <div class="row">
                              <div class="col-12 col-lg-6">
                              
                              
                              
                             <div class="section-headline">
                                 <h3>'.$lval->title.'</h3>
                             </div>
                            
                            '.$print_url.'
                          
                            '.$lval->descriptions.'
                   
                          </div>
                          <div class="col-12 col-lg-6">
                            <img src="'.$img_url.'" alt="" class="hero-img">
                          </div>
                        </div>
                      </div>';  
            unset($rating_number);
            return $singleserviceshtml;
        }
        
    }




    public function logins()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('contact');
        $sections = $page->sections;
        if(!empty(user_id()))
        {
          return redirect('client/home');
        }
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('login')
            ->data(compact('page','sections'))
            ->output();
    }
    
    public function registers()
    {

        if(!empty(user_id()))
        {
          return redirect('client/home');
        }
        return $this->response
            ->setMetaKeyword(strip_tags("Register"))
            ->setMetaDescription(strip_tags("Register"))
            ->setMetaTitle(strip_tags("Register"))
            ->layout('home')
            ->view('register')
            ->output();
    }
    public function register()
    {

        if(!empty(user_id()))
        {
          return redirect('client/home');
        }
        return $this->response
            ->setMetaKeyword(strip_tags("Register"))
            ->setMetaDescription(strip_tags("Register"))
            ->setMetaTitle(strip_tags("Register"))
            ->layout('home')
            ->view('register')
            ->output();
    }

    public function checkemail()
    {
        $email_id = $_POST['email_id'];
        $data = DB::table('clients')->where('email',$email_id)->first();
        if($data)
        {
            echo true;
        }
        else
        {
            echo false;
        }     
    }
    
    public function  productsearchsubmit(Request $request){
         $searchkeywords = $request->searchkeywords;
         $urls = url('/')."/products/?s=".$searchkeywords;
         return redirect($urls); 
    }
    public function  registers_action(Request $request)
    {
        
         $username =  $request->name;
         $password =  $request->password;
         $useremail =  $request->email;
         $verifyid =  strtolower(substr($request->name,0,2)).time();
        
            $data = [
                'name'      => $username,
                'email'     => $useremail,
                'password'  => $password,
                'api_token' => str_random(60),
                'status'    =>'Locked',
                'user_id'   =>4,
                'verify_id' =>$verifyid 
            ];

            $user  = Client::create($data);
            $values = array(
            'verify_id'=>$verifyid
            ); 

            DB::table('clients')
            ->where('email',$request->email)
            ->update($values);
        
            $subject                =  "Frontline Bergains";
            $from                   =  "info@owenGraffix.com";
            $fromName               =  "Frontline Bergains";
            $emailSubject           =  "Frontline Bergains";
            $email                  =  "developer.owengraffix@gmail.com";
            
            $img                    =  url('public/storage/uploads/frontline-logo.png');
            $url                    =  url('/verifyemail/').'/'.$verifyid;

            //admin  
            $htmlContent = "<html>
            <head>
            <title>Please Follow the Instruction</title>
              <style type='text/css'>
              .content-fixed
              {
                width:600px;         
              }
              .wrap-image
              {
                  background:#fff;
                  text-align:center;
                  padding:15px;
              }
              img
              {
                  max-width:150px;
                  max-height:150px;
              }
              .content{           
                  padding:10px;
              }
              .link_verify
              {
                background:#EA0029;
                padding:10px 15px;
                border-radius:50px;
                color:#fff!important;
                font-size:14px;
                font-weight:bold;
                margin-top:30px;
              }
              </style>
            </head>
            <body>
            <div class='content-fixed'>
                 <div class='wrap-image'>
                   <img src='$img'>
                 </div>
                 <div class='content'>
                    <p>Username: <strong>$username</strong></p>
                    <p style='font-size:13px;'>Note: Please verify to login.</p>
                    <p><a href='$url' class='link_verify'>Verify email address</a></p>
                </div>
                <div class='footer'>
                    <p>From,</p>
                    <p>Frotnline Bargains Teams</p>
                </div>
            </div>
            </body>
            </html>";

            $headers = "From: $fromName"." <".$from.">";
            $semi_rand = md5(time()); 
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
            "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n"; 
            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . $email;
            $send =  mail($useremail, $emailSubject, $message, $headers, $returnpath);   
            return redirect('auth')->with("msg",""); 
        
    }

    public function verifyemail($verifyid)
    {  
        $values = array(
            'status'=>"Active",
            'email_verified_at'=>date('Y-m-d'),
        ); 
        $update =  DB::table('clients')
        ->where('verify_id',$verifyid)
        ->update($values);
        $url = url('auth');
        if($update)
        {
            return redirect($url)->with('msg','Please login email is verified');
        }  
    }

    public function contactformsubmit(Request $request)
    {

        parse_str($_POST['alldata'], $myArray);
        $slug = $myArray['slug'];
        $contact_form = DB::table('quick_forms')->where('slug',$slug)->get();
        
        if(count($contact_form) > 0)
        {
            $subject                =  $contact_form[0]->subject;
            $from                   =  $contact_form[0]->sender;
            $fromName               =  $contact_form[0]->subject;
            $emailSubject           =  $contact_form[0]->admin_subject;
            $email                  =  $contact_form[0]->recipient;
            $mail_sent_ok           =  $contact_form[0]->mail_sent_ok;
            $toEmail                =  $contact_form[0]->recipient;
            $replaymsg              =  $contact_form[0]->message_body;
            
            $img                    =  url('public/themes/admin/assets/img/logo/banner-drug-logo.jpg');
            
            
             //thank you
                $user_cc               =  $contact_form[0]->user_cc;
                $user_subject          =  $contact_form[0]->user_subject;
                $user_from             =  $contact_form[0]->user_from;
             //thank you
            
            
            $firstname             =  $myArray['name'];
            $useremail             =  $myArray['email'];
            $number                =  $myArray['number'];
            $message               =  $myArray['message'];
            $phone                 =  $myArray['phone'];
            
//            $phoneval = preg_replace('/[^0-9]/', '', $phone);
//            if(strlen($phoneval) === 10) {
//            
//            }
//            else
//            {
//                Session::flash('phoneerror',"Phone no is invalid try again!!");
//                return redirect()->back()->with('message', 'IT WORKS!');   
//            }

          
    //Start User Into to Admin Email     
            
        $toadminEmail                   =  $contact_form[0]->toadmin;   
        $toadminSubject                 =  $contact_form[0]->admin_subject;   
        $toadminfromName                =  $contact_form[0]->admin_subject;   
        $toadmin                        =  $contact_form[0]->toadmin;   
        $toemailadmin                   =  $contact_form[0]->toadmin;   
        $admin_css                      =  $contact_form[0]->admin_css;   
        $admin_from                     =  $contact_form[0]->admin_from;   
            

        $submission            = Submission::create();
        $submission->firstname      = $firstname;
        $submission->phone     = $number;
        $submission->email     = $useremail;
       
        $submission->message   = $message;
            
        date_default_timezone_set('Europe/London');
        $submission->mail_time =  date("M-d-Y h:i:s A") . "\n";       
            

        $submission->slug     = $slug;
        $submission->status   = "active";
        $submission->save();

      //admin  
            
            
            
            
        $adminbody = '';
        $a = array(
            'firstname'=>$firstname,
            'lastname'=>$lastname,
            'email'=>$useremail,
            'subject'=>$subject,
            'phone'=>$phone,
            'message'=>$message,
            'baseurl'=>$this->baseurl
        );
            
        $adminbody_data = preg_replace_callback('~\{(.*?)\}~',
        function($key) use($a)
        {
            $variable['firstname']            = $a['firstname'];
            $variable['lastname']             = $a['lastname'];
            $variable['useremail']            = $a['email'];
            $variable['subject']              = $a['subject'];
            $variable['phone']                = $a['phone'];
            $variable['message']              = $a['message'];
            $variable['baseurl']              = $a['baseurl'];
            return $variable[$key[1]]; 
        },
        $contact_form[0]->admin_body);  
            
        $adminbody .=  $adminbody_data;
        $htmlContent = "<html>
        <head>
        <title>Please Follow the Instruction</title>
         $admin_css
        </head>
        <body>
        <div class='content-fixed'>
         $adminbody
        </div>
        </body>
        </html>";
            
        $header_content = preg_replace_callback('~\{(.*?)\}~',
        function($key) use($a)
        {
            $variable['baseurl']            = $this->baseurl;
            return $variable[$key[1]]; 
        },
        $contact_form[0]->header_content);   
            
        $thanku_template = preg_replace_callback('~\{(.*?)\}~',
        function($key) use($a)
        {
            $variable['baseurl']            = $this->baseurl;
            return $variable[$key[1]]; 
        },
        $contact_form[0]->thanku_template);       
                    
          
        $headers = "From: $toadminSubject"." <".$admin_from.">";
        $semi_rand = md5(time()); 
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
        $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
        "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n"; 
        $message .= "--{$mime_boundary}--";
        $returnpath = "-f" . $admin_from;
        $send =  mail($toadminEmail, $toadminSubject, $message, $headers, $returnpath);
        //User Get Booking Msg
//     End User Into to Admin Email   
            
        unset($message);
 
        $htmlContentuser = '<html>
        <head>
        <title>Please Follow the Instruction</title>
          <style type="text/css">';
            
        $htmlContentuser.= $contact_form[0]->template_css;  
            
        $htmlContentuser.='</style></head>';
            
        $htmlContentuser.= $header_content;  
        $htmlContentuser.= $firstname;
        $htmlContentuser.= $thanku_template;
        $htmlContentuser.= '</html>';

           // die();
//            $headers = "From: $fromName"." <".$from.">";
//            $semi_rand = md5(time()); 
//            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
//            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
//            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
//            "Content-Transfer-Encoding: 7bit\n\n" . $htmlContentuser . "\n\n"; 
//            $message .= "--{$mime_boundary}--";
//            $returnpath = "-f" . $from;
//            $usersend =  mail($useremail, $emailSubject, $message, $headers, $returnpath);
            
            
            
        $headerss = "From: $fromName"." <".$user_from.">";
        $semi_rands = md5(rand(0,1000)); 
        $mime_boundarys = "==Multipart_Boundary_x{$semi_rands}x"; 
        $headerss .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundarys}\""; 
        $messages = "--{$mime_boundarys}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
        "Content-Transfer-Encoding: 7bit\n\n" . $htmlContentuser . "\n\n"; 
        $messages .= "--{$mime_boundarys}--";
        $returnpaths = "-f" . $email;
        $usersend = mail($useremail,$user_subject,$messages,$headerss,$returnpaths);    
            
            
            
            
            if($usersend)
            { 
               echo $mail_sent_ok;
            }
            else
            {
                echo "error";
            }
            
        }
           
    }
    
    public function subscribeussubmit(Request $request)
    {

        parse_str($_POST['alldata'], $myArray);
        $slug = $myArray['slug'];
        $contact_form = DB::table('quick_forms')->where('slug',$slug)->get();
        
        if(count($contact_form) > 0)
        {
            $subject                =  $contact_form[0]->subject;
            $from                   =  $contact_form[0]->sender;
            $fromName               =  $contact_form[0]->subject;
            $emailSubject           =  $contact_form[0]->admin_subject;
            $email                  =  $contact_form[0]->recipient;
            $mail_sent_ok           =  $contact_form[0]->mail_sent_ok;
            $toEmail                =  $contact_form[0]->recipient;
            $replaymsg              =  $contact_form[0]->message_body;
            
            $img                    =  url('public/themes/admin/assets/img/logo/banner-drug-logo.jpg');
            
            
             //thank you
                $user_cc               =  $contact_form[0]->user_cc;
                $user_subject          =  $contact_form[0]->user_subject;
                $user_from             =  $contact_form[0]->user_from;
             //thank you
            
            
            $firstname             =  $myArray['name'];
            $useremail             =  $myArray['email'];
            $number                =  $myArray['number'];
            $message               =  $myArray['message'];
  
            
        $toadminEmail                   =  $contact_form[0]->toadmin;   
        $toadminSubject                 =  $contact_form[0]->admin_subject;   
        $toadminfromName                =  $contact_form[0]->admin_subject;   
        $toadmin                        =  $contact_form[0]->toadmin;   
        $toemailadmin                   =  $contact_form[0]->toadmin;   
        $admin_css                      =  $contact_form[0]->admin_css;   
        $admin_from                     =  $contact_form[0]->admin_from;   
            

        $submission            = Submission::create();
        $submission->firstname      = $firstname;
        $submission->phone     = $number;
        $submission->email     = $useremail;
       
        $submission->message   = $message;
            
        date_default_timezone_set('Europe/London');
        $submission->mail_time =  date("M-d-Y h:i:s A") . "\n";       
            

        $submission->slug     = $slug;
        $submission->status   = "active";
        $submission->save();

      //admin  
 
        $adminbody = '';
        $a = array(
            'firstname'=>$firstname,
            'lastname'=>$lastname,
            'email'=>$useremail,
            'subject'=>$subject,
            'message'=>$message,
            'baseurl'=>$this->baseurl
        );
            
        $adminbody_data = preg_replace_callback('~\{(.*?)\}~',
        function($key) use($a)
        {
            $variable['firstname']            = $a['firstname'];
            $variable['lastname']             = $a['lastname'];
            $variable['useremail']            = $a['email'];
            $variable['subject']              = $a['subject'];
            $variable['message']              = $a['message'];
            $variable['baseurl']              = $a['baseurl'];
            return $variable[$key[1]]; 
        },
        $contact_form[0]->admin_body);  
            
        $adminbody .=  $adminbody_data;
        $htmlContent = "<html>
        <head>
        <title>Please Follow the Instruction</title>
         $admin_css
        </head>
        <body>
        <div class='content-fixed'>
         $adminbody
        </div>
        </body>
        </html>";
            
        $header_content = preg_replace_callback('~\{(.*?)\}~',
        function($key) use($a)
        {
            $variable['baseurl']            = $this->baseurl;
            return $variable[$key[1]]; 
        },
        $contact_form[0]->header_content);   
            
        $thanku_template = preg_replace_callback('~\{(.*?)\}~',
        function($key) use($a)
        {
            $variable['baseurl']            = $this->baseurl;
            return $variable[$key[1]]; 
        },
        $contact_form[0]->thanku_template);       
                    
          
        $headers = "From: $toadminSubject"." <".$admin_from.">";
        $semi_rand = md5(time()); 
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
        $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
        "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n"; 
        $message .= "--{$mime_boundary}--";
        $returnpath = "-f" . $admin_from;
        $send =  mail($toadminEmail, $toadminSubject, $message, $headers, $returnpath);
        //User Get Booking Msg
//     End User Into to Admin Email   
            
        unset($message);
 
        $htmlContentuser = '<html>
        <head>
        <title>Please Follow the Instruction</title>
          <style type="text/css">';
            
        $htmlContentuser.= $contact_form[0]->template_css;  
            
        $htmlContentuser.='</style></head>';
            
        $htmlContentuser.= $header_content;  
        $htmlContentuser.= $firstname;
        $htmlContentuser.= $thanku_template;
        $htmlContentuser.= '</html>';     
            
        $headerss = "From: $fromName"." <".$user_from.">";
        $semi_rands = md5(rand(0,1000)); 
        $mime_boundarys = "==Multipart_Boundary_x{$semi_rands}x"; 
        $headerss .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundarys}\""; 
        $messages = "--{$mime_boundarys}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
        "Content-Transfer-Encoding: 7bit\n\n" . $htmlContentuser . "\n\n"; 
        $messages .= "--{$mime_boundarys}--";
        $returnpaths = "-f" . $email;
        $usersend = mail($useremail,$user_subject,$messages,$headerss,$returnpaths);    

            
            if($usersend)
            { 
               echo $mail_sent_ok;
            }
            else
            {
                echo "error";
            }
            
        }
           
    }
    public function shortcodes_review()
    {
        $reviews = Review::where('status','Published')->where('id',7)->first();  
        if(count($reviews) > 0):
        
        $html  ='';
        $html .='<div class="rating" onmouseover="ratingHover()"><div class="rating-inner">
            <span class="header">'.$reviews->title.'</span>
            <p> <span class="rate-number">'.$reviews->rating.'</span></p>';
            for($x=0;$x<$reviews->ratingnumber;$x++)
            {
                $html .= '<img src="'.url('public/themes/public/assets/img/icons/star-yellow.svg').'" alt="5 star rating" class="star-icon">'; 
            }
        $html .='</div></div>';
        endif;
        return $html;
        
    }
    
    public function shortcode_popularproducts(){

        $product_cat              = Productcategories::where('status','published')->where('home_features','yes')->get();
        $products_all             = Products::where('status','published')->get();

        
        foreach($product_cat as $keys=>$vals){
            $product_ac_cat[$vals->slug] = Products::where('status','Published')->where('category','like','%'.$vals->slug.'%')->get();
        }
         
      $html = '';
      $html .= '<ul class="fr-pop-tabs sections-show">
                <li><a data-frpoptab-num="1" data-frpoptab="#frpoptab-tab-1" href="#" class="active">All Categories</a></li>';
        
                $cnt_cat = 2;
                foreach($product_cat as $pr_cat=>$pr_val){
                    $html .= '<li><a data-frpoptab-num="'.$cnt_cat++.'" data-frpoptab="#'.$pr_val->slug.'" href="#">'.$pr_val->title.'</a></li>';
                }
        
        
             $html .= '</ul>
            
            <div class="fr-pop-tab-cont">
                 <p data-frpoptab-num="1" class="fr-pop-tab-mob active" data-frpoptab="#frpoptab-tab-1">All Categories</p>
                 <div class="flexslider prod-items fr-pop-tab" id="frpoptab-tab-1">

                    <ul class="slides">';
                      
                        foreach($products_all as $allkeys=>$allval){
                            $class_cross="";
                            if($allval->saleprice){
                                $class_cross = "cross_price";
                            }
                            
                            if($allval->image){
                                $image = '<img src="'.url('/').'/'.$allval->image.'">';
                            }
                            else{
                                $image = '<img src="'.theme_asset('/').'/images/placeholder.png">';
                            }
                            if($allval->saleprice){
                               $saleprice = "$".$allval->saleprice;
                            }
                            else{
                               $saleprice = ""; 
                            }
                            $html .= '<li class="prod-i">
                                    <div class="prod-i-top">
                                        <a href="'.url('single-products').'/'.$allval->slug.'" class="prod-i-img"><!-- NO SPACE -->'.$image.'<!-- NO SPACE --></a>
                                        
                                        <p class="prod-i-info" style="text-align:center;">
                                            <a href="javascript:void(0)" class="qview-btn prod-i-qview" id="'.$allval->id.'"><span>Quick View</span><i class="fa fa-search"></i></a>
                                        </p>
                                        <p class="prod-i-addwrap">
                                            <a href="'.url('single-products').'/'.$allval->slug.'" class="prod-i-add">Go to detail</a>
                                        </p>
                                        
                                    </div>
                                    <h3>
                                        <a href="'.url('single-products').'/'.$allval->slug.'">'.$allval->title.'</a>
                                    </h3>
                                    <p class="prod-i-price">
                                        <b> <span class="regular_price '.$class_cross.'">$'.$allval->price.' </span> &nbsp;<span class="sale_price">'.$saleprice.'</span></b>
                                    </p>
                            </li>';
                            
                        }
                        
                       
                    $html .='</ul>
                </div>';
               
                $cnt = 2;
                foreach($product_ac_cat as $productskey=>$productdata){      
                $html .= '<p data-frpoptab-num="'.$cnt++.'" class="fr-pop-tab-mob" data-frpoptab="#'.$productskey.'">'.$productskey.'</p>
                <div class="flexslider prod-items fr-pop-tab" id="'.$productskey.'">
                    <ul class="slides">';
                    
                       foreach($productdata as $keys=>$vals){
                          if($vals->image){
                                $image = '<img src="'.url('/').'/'.$vals->image.'">';
                            }
                            else{
                                $image = '<img src="'.theme_asset('/').'/images/placeholder.png">';
                            }
                            $html .= '<li class="prod-i">
                            
                            <div class="prod-i-top">
                                <a href="'.url('single-products').'/'.$vals->slug.'" class="prod-i-img"><!-- NO SPACE -->'.$image.'<!-- NO SPACE --></a>
                                <p class="prod-i-info" style="text-align:center;">
                                    <a href="javascript:void(0)" class="qview-btn prod-i-qview" id="'.$allval->id.'"><span>Quick View</span><i class="fa fa-search"></i></a>
                                </p>
                                <p class="prod-i-addwrap">
                                      <a href="'.url('single-products').'/'.$vals->slug.'" class="prod-i-add">Go to detail</a>
                                </p>
                            </div>
                            <h3>
                                 <a href="'.url('single-products').'/'.$vals->slug.'">'.$vals->title.'</a>
                            </h3>
                            <p class="prod-i-price">
                                <b>$'.$vals->price.'</b>
                            </p>
                        </li>';   
                       }
                    
                   $html .= '</ul>
                </div>';
                }
                
              
           $html .= '</div>';
           return $html; 
    }
    
    public function shortcode_specialoffer(){
        
        $products_hotitem             = Products::where('status','published')->where('home_hotitem','yes')->get();
        $html = '<div class="flexslider discounts-list">
             <ul class="slides">';   
             foreach($products_hotitem as $hKeys=>$hvals){
                if($hvals->image){
                    $image = url('/').'/'.$hvals->image;
                }
                else{
                    $image = theme_asset('/').'/images/placeholder.png';
                }

                $html .= '<li class="discounts-i">
                            <a href="'.url('single-products').'/'.$hvals->slug.'" class="discounts-i-img">
                                <img src="'.$image.'" alt="'.$hvals->title.'">
                            </a>
                            <h3 class="discounts-i-ttl">
                                <a href="'.url('single-products').'/'.$hvals->slug.'">'.$hvals->title.'</a>
                            </h3>
                            <p class="discounts-i-price">
                                <b>$'.$hvals->price.'</b>
                            </p>
                        </li>';
             }
             $html .= '</ul>
             </div>';
        return $html;
    }
        
    public function shortocode_poster(){
        
        
        $poster = Whybannerdrugs::where('status','published')->get();
        foreach($poster as $pkeys=>$pvals){
            if($pvals->icons){
                $image = url('/').'/'.$pvals->icons;
            }
            else{
                $image = theme_asset('/').'/images/placeholder.png';
            }

            $html .= '<div class="posts-i">
                <a class="posts-i-img" href="'.$pvals->links.'">
                    <img src="'.$image.'">
                </a>
            </div>';   
        }
        return $html;
        
        
        
    }  
        
    
    public function shortcode_popularbanner(){
        
        $product_cat   = Productcategories::where('status','published')->where('home_features','yes')->get();
        $html = '';
        foreach($product_cat as $pckeys=>$pcvals){

        if($pcvals->image){
            $image = url('/').'/'.$pcvals->image;
        }
        else{
            $image = theme_asset('/').'/images/placeholder.png';
        }
        $html .= '<div class="banner-i style_22">
             <span class="banner-i-bg" style="background: url('.$image.')"></span>
             <div class="banner-i-cont">
                <p class="banner-i-subttl">'.$pcvals->title.'</p>
                <h3 class="banner-i-ttl">'.$pcvals->subtitle.'</h3>
                <p>'.$pcvals->descriptions.'</p>
                <p class="banner-i-link"><a href="'.url('/products').'/'.$pcvals->slug.'">View More</a></p>
            </div>
        </div>';
        }
        return $html;  
    }
    public function shortcode_subscribe_forms(){
        $subscribeForm      = QuickForm::where('slug','contact-us')->get();
        $subscribe_html = '';
        foreach($subscribeForm as $tkey=>$tval)
        {
            $subscribe_html .= $tval->form_field;
        }
        return $subscribe_html;
    }
    public function shortcode_contact_forms(){
        $subscribeForm  = QuickForm::where('slug','contact-us---contact-page')->get();
        $subscribe_html = '';
        foreach($subscribeForm as $tkey=>$tval)
        {
            $subscribe_html .= $tval->form_field;
        }
        return $subscribe_html;
    }
    
    public function shortcode_socialmedia(){
        $getlinks = Menu::where('parent_id',6)->orderBy('order','asc')->get();
        $html = '';
        foreach($getlinks as $skeys=>$svals){
        $html .= '<div class="social-i">
                    <a rel="nofollow" target="_blank" href="'.$svals->url.'">
                        <p class="social-i-img">
                            <i class="'.$svals->icon.'"></i>
                        </p>
                        <p class="social-i-ttl">'.$svals->name.'</p>
                    </a>
                </div>';
        }
        return $html;
    }
    public function shortcode_brandscarry(){
        $data = Services::where('status','Published')->get();
        $html = '<div class="flexslider brands-list">
                <ul class="slides">';
                    foreach($data as $keys=>$vals){
                        $html .= '<li>
                            <img src="'.url('/').'/'.$vals->icons.'">
                        </li>';
                    }
                $html .='</ul>
            </div>';
       
            return $html;
    }
    public function pagesections($pageid,$servicesslug="",$locationslug="",$teammembername=""){

        $servicesdata                         = Services::where('slug',$servicesslug)->get();
        $locationdata                         = Location::where('slug',$locationslug)->get();
        
        foreach($locationdata as $lkey=>$locval){
              $store_name = $locval->title;
        }
        
        foreach($servicesdata as $lkey=>$serval){
              $services_name = $serval->title;
              $services_status = $serval->status;
                if( $services_status == "Draft")
                {
                    return redirect('404');
                }
        }

        $sections                    = FrontSection::where('page_id',$pageid)->orderBy('order_by','asc')->get();
        $qucikformhtml               = $this->getformbyslug('contact-us');
        $qucikform_contact_us        = $this->getformbyslug('contact-us---contact-page');
        
        $bannersectionshtml          = $this->bannersections();
        $testimonailshtml            = $this->gettestimonilas();
        $servicesshtml               = $this->getservices();
        $locationshtml               = $this->getlocations();
        $teammemberhtml              = $this->getteammember();
        $teammembergridhtml          = $this->getteammembergrid();
        
        
        $teammemberbylocation        = $this->getteammemberbylocation($locationslug);
        
        $servicesservicepagehtml     = $this->getservices_servicepage();
        $singlelocationhtml          = $this->getsinglestoreinfo($locationslug);
        $singleserviceshtml          = $this->getsingleservicesinfo($servicesslug); 
        $faqshtml                    = $this->getfaqs(3);
        $allfaqshtml                 = $this->getallfaqs();
        $alltestimonailshtml         = $this->gettestimonilasall();
        $shortcodes_review           = $this->shortcodes_review();
        $shortcodes_memberinfo       = $this->getteaminfo($teammembername);
        $allblogsshortocodehtml      = $this->allblogsshortocode();
        $shortcode_popularproducts   = $this->shortcode_popularproducts();
        $shortcode_subscribe_forms   = $this->shortcode_subscribe_forms();
        $shortcode_contact_forms     = $this->shortcode_contact_forms();
        $shortcode_socialmedia       = $this->shortcode_socialmedia();
        $shortcode_brandscarry       = $this->shortcode_brandscarry();
        $shortcode_popularbanner     = $this->shortcode_popularbanner();
        $shortcode_specialoffer      = $this->shortcode_specialoffer();
        $shortocode_poster           = $this->shortocode_poster();
        
        
        $a  = array(
                'baseurl'                  =>$this->baseurl,
                'testimonials'             =>$testimonailshtml,
                'services'                 =>$servicesshtml,
                'qucikformhtml'            =>$qucikformhtml,
                'bannersectionshtml'       =>$bannersectionshtml,
                'teammemberhtml'           =>$teammemberhtml,
                'teammembergrid'           =>$teammembergridhtml,
                'locationshtml'            =>$locationshtml,
                'servicesservicepagehtml'  =>$servicesservicepagehtml,
                'services_name'            =>$services_name,
                'singlelocationhtml'       =>$singlelocationhtml,
                'singleservices'           =>$singleserviceshtml,
                'storename'                =>$store_name,
                'faqshtml'                 =>$faqshtml,
                'allfaqshtml'              =>$allfaqshtml,
                'alltestimonials'          =>$alltestimonailshtml,
                'shortcodes_review'        =>$shortcodes_review,
                'memberinfo'               =>$shortcodes_memberinfo,
                'qucikform_contact_us'           =>$qucikform_contact_us,
                'teammemberbylocation'           =>$teammemberbylocation,
                'allblogs'                       =>$allblogsshortocodehtml,
                'shortcode_popularproducts'      =>$shortcode_popularproducts,
                'subscribeforms'                 =>$shortcode_subscribe_forms,
                'shortcode_socialmedia'          =>$shortcode_socialmedia,
                'shortcode_contact_forms'        =>$shortcode_contact_forms,
                'brandswecarry'                  =>$shortcode_brandscarry,
                'shortcode_popularbanner'        =>$shortcode_popularbanner,
                'shortcode_specialoffer'         =>$shortcode_specialoffer,
                'shortocode_poster'              =>$shortocode_poster,
           ); 
        $htmlcontent = '';

        foreach($sections as $keys=>$values)
        {
            $img_url = url('/').'/'.$values->image;
            $section_data = array(
                'name'     =>$values->name,
                'heading'  =>$values->heading,
                'image'    =>$img_url,
                'sectionid'=>$values->sectionid,
                'classname'=>$values->sectionclassname
            ); 
            $htmlcontent .= '<div class="'.$values->sectionclassname.'" id="'.$values->sectionid.'">';
            $htmlfilters = preg_replace_callback('~\{(.*?)\}~',
            function($key) use($a,$section_data)
            {
                $variable['heading']                  = $section_data['name'];
                $variable['subheading']               = $section_data['heading'];
                $variable['imageurl']                 = $section_data['image'];
                $variable['id']                       = $section_data['sectionid'];
                $variable['classname']                = $section_data['classname'];

                
                $variable['sliderservices']           = $a['services'];
                $variable['slidertestimonials']       = $a['testimonials'];
                $variable['contactform']              = $a['qucikformhtml'];
                $variable['qucikform_contact_us']     = $a['qucikform_contact_us'];
                
                $variable['bannersections']           = $a['bannersectionshtml'];
                $variable['teammember']               = $a['teammemberhtml'];
                $variable['locations']                = $a['locationshtml'];
                $variable['singlestrore']             = $a['singlelocationhtml'];
                $variable['allservices']              = $a['servicesservicepagehtml'];
                $variable['servicesname']             = $a['services_name'];
                $variable['singlestrore']             = $a['singlelocationhtml'];
                $variable['singleservices']           = $a['singleservices'];
                $variable['singlestrore']             = $a['singlelocationhtml'];
                $variable['storename']                = $a['storename'];
                
                $variable['faqs']                     = $a['faqshtml'];
                $variable['allfaqs']                  = $a['allfaqshtml'];
                $variable['alltestimonials']          = $a['alltestimonials'];
                $variable['googlerating']             = $a['shortcodes_review'];
                $variable['memberinfo']               = $a['memberinfo'];
                $variable['baseurl']                  = $a['baseurl'];
                $variable['teammemberbylocation']     = $a['teammemberbylocation'];
                $variable['teammembergrid']           = $a['teammembergrid'];
                $variable['allblogs']                 = $a['allblogs'];
                $variable['popularproducts']          = $a['shortcode_popularproducts'];
                $variable['subscribeforms']           = $a['subscribeforms'];
                $variable['socialmedia']              = $a['shortcode_socialmedia'];
                $variable['contact_forms']              = $a['shortcode_contact_forms'];
                $variable['brandswecarry']              = $a['brandswecarry'];
                $variable['shortcode_popularbanner']    = $a['shortcode_popularbanner'];
                $variable['shortcode_specialoffer']     = $a['shortcode_specialoffer'];
                $variable['shortocode_poster']         = $a['shortocode_poster'];
                return $variable[$key[1]]; 

            },
            $values->body); 
            unset($img_url);  
            $htmlcontent .=  $htmlfilters;
            $htmlcontent .=  '</div>';  
           
        }
  
        $htmlfilters =  $htmlcontent;
        return $htmlfilters;

    }
    
    

    
    public function advanced_filters(){
        error_reporting(0);
        $where = [];
        
        parse_str($_POST['alldata'], $myArray);
   
        $fromprice           = (int)$myArray['fromprice'];
        $toprice             = (int)$myArray['torice'];
        $current_category    = $myArray['current_category'];
        
        $keywords    = $myArray['keywords'];
        
        
        $query = Products::query();
        
        $query = $query->where('status','Published');
        
        if($current_category){
            $query = $query->where('category', 'like','%'.$current_category.'%');  
        }
        if($keywords){
             $query = $query->where('title', 'like','%'.$keywords.'%');  
        }
        
        $query     = $query->whereBetween('price',[$fromprice, $toprice]);
        $products  = $query->orderby('id','desc')->get();
        $html      = '<ul id="results">';
        
        foreach($products as $allkeys=>$allval){
            
                $class_cross="";
                if($allval->saleprice){
                    $class_cross = "cross_price";
                }
                if($allval->image){
                    $image = '<img src="'.url('/').'/'.$allval->image.'">';
                }
                else{
                    $image = '<img src="'.theme_asset('/').'/images/placeholder.png">';
                }
                if($allval->saleprice){
                    $saleprice = "$".$allval->saleprice;
                }
                else{
                  $saleprice = "";  
                }
            $html .= '<li class="prod-i pro-link" id="'.$allval->price.'" title="'.$allval->title.'" role="">
                <div class="prod-i-top">
                    <a href="'.url('single-products').'/'.$allval->slug.'" class="prod-i-img"><!-- NO SPACE -->'.$image.'<!-- NO SPACE --></a>
                    <p class="prod-i-info" style="text-align:center;">
						<a href="javascript:void(0)" class="qview-btn prod-i-qview" id="'.$allval->id.'"><span>Quick View</span><i class="fa fa-search"></i></a>
					</p>
                    <a href="'.url('addToCart').'/'.$allval->id.'" class="prod-i-buy" rel="nofollow">Add to cart</a>
                    <p class="prod-i-properties-label"><i class="fa fa-info"></i></p>
                </div>
                <h3>
                   <a href="'.url('single-products').'/'.$allval->slug.'" class="prod-i-add">'.$allval->title.'</a>
                </h3>
                <p class="prod-i-price">
                      <b> <span class="regular_price '.$class_cross.'">$'.$allval->price.' </span> &nbsp;<span class="sale_price">'.$saleprice.'</span></b>
                </p>
            </li>';  
        }
        echo '</ul>';
        echo $html;
    }
    public function getquickview(){
        $product_id = $_POST['alldata'];
        $data = Products::find($product_id);
  
        if($data['image']){
            $url = url('/').'/'.$data['image'];
            $image = '<img src="'.url('/').'/'.$data['image'].'">';
        }
        else{
            $url   = theme_asset('/').'/images/placeholder.png';
            $image = '<img src="'.theme_asset('/').'/images/placeholder.png">';
        } 
            $class_cross="";
          
            if($data['saleprice']){
                $saleprice = "$".$data['saleprice'];
                $class_cross = "cross_price";
            }
            else{
                $saleprice = "";  
            }
        
            $gallery = "";
            if($data['gallery']){
                $gallery_array = unserialize($data['gallery']);
                if(count($data['gallery']) > 0){
                    
                  foreach($gallery_array as $gkeys=>$gvals){
                    $gallery .= '<li>
                            <a class="fancy-img" href="'.url('/').'/'.$gvals.'">
                               <img src="'.url('/').'/'.$gvals.'">
                            </a>
                        </li>';

                  }  
                }

            }
             $html = '<div class="prod-wrap custom_quickview">
		    	<a href="'.url('single-products').'/'.$data['slug'].'">
					<h1 class="main-ttl">
						<span>'.$data['title'].'</span>
					</h1>
				</a>
				<div class="prod-slider-wrap">
					<div class="prod-slider">
						<ul class="prod-slider-car">
							<li>
								<a  class="fancy-img" href="'.$url.'">
									'.$image.'
								</a>
							</li>
				            '.$gallery.'
						</ul>
					</div>
	
				</div>

				<div class="prod-cont">
					
    				<div class="prod-cont-txt">
    					'.$data['descriptions'].'
    				</div>
					<div class="prod-info">
						<p class="prod-price">
                        
                            <b> <span class="regular_price '.$class_cross.'">$'.$data['price'].' </span> &nbsp;<span class="sale_price">'.$saleprice.'</span></b>
						
						</p>
					
						<p class="prod-addwrap">
						        <a href="'.url('addToCart').'/'.$data['id'].'" class="prod-add" rel="nofollow">Add to cart</a>
						</p>
					</div>
			
				</div>
			</div>';
       
		echo $html;
       
    }
    
    public function forgot_password()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('enforgot-password');
        $sections = $page->sections;
        if(!empty(user_id()))
        {
          return redirect('client/home');
        }
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('forgot')
            ->data(compact('page','sections'))
            ->output();
    } 
    public function forgotemails()
    {
        $email = $_POST['email'];
        $data  = DB::table('clients')->where('email',$email)->first();
        
 
        $values = array(
            'session_date'=>date('Y-m-d H:i:s'),
        );
        if(count($data))
        {
            
            $verify_id =  $data->verify_id;
            $uname     =  $data->name;
            $useremail = $email;
            DB::table('clients')
            ->where('email',$email)
            ->update($values);
            
            $subject                =  "Frontline Bargains - Update Password";
            $from                   =  "admin@frontlinebargains.com";
            $fromName               =  "Frontline Bargains";
            $emailSubject           =  "Frontline Bargains";
            $email                  =  "developer.owengraffix@gmail.com";
            
            
            $img                    =  url('public/storage/uploads/frontline-logo.png');
            
            
            $url                    =  url('/update_password/').'/'.$verify_id.'?id='.uniqid();
            //admin  
            $htmlContent = "<html>
            <head>
            <title>Please Follow the Instruction</title>
              <style type='text/css'>
              body p
              {
                color:#000;
              }
              .content-fixed
              {
                width:600px;         
              }
              .wrap-image
              {
    
                  text-align:center;
                  padding:15px;
              }
              img
              {
                  max-width:150px;
                  max-height:150px;
              }
              .content{           
                  padding:10px;
              }
              .link_verify
              {
                margin-top:30px;
                color:#000!important;
                font-size:15px;
                font-weight:bold;
              }
              </style>
            </head>
            <body>
            <div class='content-fixed'>
                 <div class='wrap-image'>
                   <img src='$img'>
                 </div>
                 <div class='content'>
                    <p>Username:<strong>$uname</strong></p>
                
                    <p>Note:Please click to below link to update password.</p>
                    <p><a href='$url' class='link_verify'>Update Password</a></p>
                    
                    <p>Frontline Bargains Team.</p>
                </div>
            </div>
            </body>
            </html>";


            $headers = "From: $fromName"." <".$from.">";
            $semi_rand = md5(time()); 
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
            "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n"; 
            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . $email;
            $send =  mail($useremail, $emailSubject, $message, $headers, $returnpath);   
            echo "true";
        }
        else
        {
            echo "false";
        }

       
    }
    public function updatepasswords($slugid="")
    { 
 
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('contact');
        $sections = $page->sections;
        if(!empty(user_id()))
        {
          return redirect('client/home');
        }
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('updatepassword')
            ->data(compact('page','sections','slugid'))
            ->output();
    }
    public function customer_updatepaswords()
    {
         $password  = $_POST['password'];
         $verify_id = $_POST['verifyid'];
         $data  = DB::table('clients')->where('verify_id',$verify_id)->first();
         $session_date =  strtotime($data->session_date);
         $current_date =  strtotime(date('Y-m-d H:i:s'));
            $values = array(
                'password'=>Hash::make($password),
            );
        $update =     DB::table('clients')
        ->where('verify_id',$verify_id)
        ->update($values);
        if($update)
        {
            echo "true";
        }
        else
        {
           echo "false"; 
        }
    }
    
        
    public function update_cart_action(Request $request){
        echo "test";
        
        die();
    }
}
