<?php

namespace App\Console\Commands;

use App\Models\Definitions\Taxonomy;
use App\Models\Definitions\TaxonomyLang;
use App\Models\Definitions\Term;
use App\Models\Definitions\TermLang;
use App\Models\Settings\Language;
use App\Models\Settings\Partner;
use App\Models\Settings\PartnerLang;
use Illuminate\Console\Command;

class LangSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes missing translations for taxonomy items.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $defaultLanguage = Language::where('is_default', 1)->first();
        $languages = Language::all();

        $taxonomies = Taxonomy::all();

        foreach ($taxonomies as $taxonomy) {
            foreach ($languages as $language) {

                if (TaxonomyLang::where('taxonomy_id', $taxonomy->id)->where('lang_id', $language->id)->first() === null) {

                    $defaultValue = TaxonomyLang::where('taxonomy_id', $taxonomy->id)->where('lang_id', $defaultLanguage->id)->first();

                    $model = new TaxonomyLang;
                    $model->taxonomy_id = $taxonomy->id;
                    $model->lang_id = $language->id;
                    $model->name = $defaultValue->name;
                    $model->deleted = 0;
                    $model->save();
                }
            }

            $terms = Term::where('taxonomy_id', $taxonomy->id)->get();

            if ($terms)
                foreach ($terms as $term) {
                    foreach ($languages as $language) {

                        if (TermLang::where('term_id', $term->id)->where('lang_id', $language->id)->first() === null) {

                            $defaultValue = TermLang::where('term_id', $term->id)->where('lang_id', $defaultLanguage->id)->first();

                            if (!$defaultValue) {
                                $defaultValue = new TermLang;
                                $defaultValue->slug = '';
                                $defaultValue->name = '';
                            }

                            $model = new TermLang;
                            $model->term_id = $term->id;
                            $model->lang_id = $language->id;
                            $model->slug = $defaultValue->slug;
                            $model->name = $defaultValue->name;
                            $model->deleted = 0;
                            $model->save();
                        }
                    }
                }
        }

        $partners = Partner::all();
        foreach ($partners as $partner) {
            foreach ($languages as $language) {

                if (PartnerLang::where('partner_id', $partner->id)->where('lang_id', $language->id)->first() === null) {

                    $defaultValue = PartnerLang::where('partner_id', $partner->id)->where('lang_id', $defaultLanguage->id)->first();

                    $model = new PartnerLang;
                    $model->partner_id = $partner->id;
                    $model->lang_id = $language->id;
                    $model->slug = $defaultValue->slug;
                    $model->name = $defaultValue->name;
                    $model->full_name = $defaultValue->full_name;
                    $model->url = $defaultValue->url;
                    $model->description = $defaultValue->description;
                    $model->save();
                }
            }
        }
        return 1;
    }
}
