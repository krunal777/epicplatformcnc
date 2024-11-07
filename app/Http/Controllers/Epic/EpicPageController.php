<?php

namespace App\Http\Controllers\Epic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Epic\EpicPage;
use App\Epic\EpicConfigurationStorage;
use App\Epic\EpicCategories;
use App\Epic\EpicBrands;
use App\Epic\EpicThemeTemplates;
use Illuminate\Support\Collection;
use Futureecom\Foundation\Tenancy\StoreRepository;
use Futureecom\Utils\Tenancy\Organisation;
use Futureecom\Utils\SystemSettings\Facades\Settings;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Epic\EpicProduct;

class EpicPageController extends Controller
{
    public function __construct()
    {
        
    }
    public function get_fields_by_page_slug(Request $request){
        $rules = [
            'slug' => 'required',
        ];
        $org = $request->header('X-Organisation-Id');
        $store = $request->header('X-Store-Id');
        $combine = $org.'-'.$store;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            // Return JSON response with validation errors
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }
        $page = EpicPage::where('path', $request->input('slug'))
        ->where('organisation', (int)$org)
        ->where('store', (int)$store)
        ->orderBy('updated_at', -1)
        ->first(); 
        if ($page) {
            $fields = $page->fields;
            return response()->json(['success' => true, 'fields' => $fields], 200);
        }
        return response()->json(['success' => false, 'message' => 'Page or slug not provided or not found'], 404);
    }
    public function sitemap(Request $request){
        $org = $request->header('X-Organisation-Id');
        $store = $request->header('X-Store-Id');
        $combine = $org.'-'.$store;
        $record = EpicConfigurationStorage::where('service', 'system')
        ->where('group', 'stores')
        ->where('name', 'template')
        ->where('tenancy', $combine)
        ->first();
        $store = $record ? $record->value : null;
        if($store){
            $template = EpicThemeTemplates::where('code', $store)->first(); 
            $template_id = $template ? $template->id : null;
            if($template_id){
                $pages = EpicPage::where('template_id', $template_id)->select('name', 'path')->get(); 
                return response()->json($pages);
            }
        }
    }
    public function categorySitemap(Request $request){
        $org = $request->header('X-Organisation-Id');
        $store = $request->header('X-Store-Id');
        $record = EpicCategories::where('organisation', (int)$org)->where('store',(int)$store)->orderBy('name', 'asc')->select('name', 'slug')->get();
        return response()->json($record);
    }
    public function productSitemap(Request $request){
        $org = $request->header('X-Organisation-Id');
        $store = $request->header('X-Store-Id');
        $perPage = $request->input('per_page', 10);

        $record = EpicProduct::where('organisation', (int)$org)->where('store',(int)$store)->orderBy('name', 'asc')->select('name', 'slug','sku','price','id')->paginate($perPage);
        return response()->json($record);
    }
    public function brandsSitemap(Request $request){
        $org = $request->header('X-Organisation-Id');
        $store = $request->header('X-Store-Id');
        $record = EpicBrands::where('organisation', (int)$org)->where('store',(int)$store)->orderBy('name', 'asc')->select('name', 'slug')->get();
        return response()->json($record);
    }
    public function emailSmtpSend(Request $request){
        $rules = [
            'from' => 'required|email',
            'to' => 'required|email',
            'subject' => 'required|string',
            'body' => 'required|string',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }




        $org = $request->header('X-Organisation-Id');
        $store = $request->header('X-Store-Id');
        $combine = $org.'-'.$store;
        $records = EpicConfigurationStorage::where('service', 'notifications')
        ->where('group', 'smtp')
        ->where('tenancy', $combine)
        ->get()->toArray();
        $smtpConfig = [];

        // Loop through $records to map the configuration values
        foreach ($records as $record) {
            if ($record['group'] === 'smtp') {
                switch ($record['name']) {
                    case 'email_from':
                        $smtpConfig['from_address'] = $record['value'];
                        break;
                    case 'name_from':
                        $smtpConfig['from_name'] = $record['value'];
                        break;
                    case 'host':
                        $smtpConfig['host'] = $record['value'];
                        break;
                    case 'port':
                        $smtpConfig['port'] = $record['value'];
                        break;
                    case 'tls':
                        $smtpConfig['encryption'] = $record['value'] === '1' ? 'tls' : null;
                        break;
                    case 'username':
                        $smtpConfig['username'] = $record['value'];
                        break;
                    case 'password':
                        $smtpConfig['password'] = $record['value'];
                        break;
                }
            }
        }
        if(!empty($smtpConfig)):
        // Set SMTP details dynamically using config()
        config([
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => $smtpConfig['host'] ?? 'default_host',
            'mail.mailers.smtp.port' => $smtpConfig['port'] ?? 587,
            'mail.mailers.smtp.encryption' => $smtpConfig['encryption'] ?? 'tls',
            'mail.mailers.smtp.username' => $smtpConfig['username'] ?? 'default_username',
            'mail.mailers.smtp.password' => $smtpConfig['password'] ?? 'default_password',
            'mail.from.address' => $smtpConfig['from_address'] ?? 'default_email@example.com',
            'mail.from.name' => $smtpConfig['from_name'] ?? 'Default Name',
        ]);
        endif;
        
        $from = $request->from;
        $toEmail = $request->to;
        $subject = $request->subject;
        $body = $request->body;
        $attachment = $request->attachment;
       
        try {
            // Send the email
            Mail::send([], [], function ($message) use ($from, $toEmail, $subject, $body, $attachment) {
                $message->from($from)
                ->to($toEmail)
                ->subject($subject)
                ->html($body);
                // Optional: Attach a file
                if($attachment){
                    $message->attach($attachment);
                }
            });
    
            return response()->json(['message' => 'Email sent successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getStoreData()
    {
        $collection = new Collection();

        $list = Settings::get('system.organisations.list', 0, []);
        foreach ($list as $organisation) {
            $storeRepo = new StoreRepository(
                new Organisation(
                    $organisation['id'],
                    $organisation['name'],
                )
            );

            $organisation['stores'] = $storeRepo->all()->values()->toArray();
            $organisation['domains'] = Settings::get('system.organisations.domains', $organisation['id'], []);

            $collection->put($organisation['id'], $organisation);
        }

        return response(['data' => $collection->toArray()]);
    }
}