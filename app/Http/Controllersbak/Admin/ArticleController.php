<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/15 0015
 * Time  : 16:02
 */
namespace App\Http\Controllers\Admin;
use App\Entity\ArticleCategory;
use App\Models\Article;
use App\Models\Brand;
use App\Models\Menu;
use App\Tools\M3Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class 后台 文章管理控制器
 */
class ArticleController extends CommonController
{
    public $ViewData = array(); /*传递页面的数组*/

    /**
     * 文章分类 首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function CategoryIndex()
    {
        /*初始化*/
        $article  = new Article();

        $this->ViewData['nav_position'] = Menu::getAdminPosition();/*面包屑*/
        $this->ViewData['category_tree'] = $article->getArticleCategoryTree();

        return view('admin.article_category_index',$this->ViewData);
    }

    /**
     * 文章分类 新增与编辑页面
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function CategoryView($id = 0)
    {
        /*初始化*/
        $article  = new Article();
        $this->ViewData['category_info'] = null;

        $this->ViewData['category_tree'] = $article->getArticleCategoryTree();

        if($id > 0)
        {
            $this->ViewData['category_info'] = ArticleCategory::findOrFail($id);
            if($this->ViewData['category_info']->parent_id > 0)
            {
                $this->ViewData['category_info']->parent_info = ArticleCategory::findOrFail($this->ViewData['category_info']->parent_id);

            }
            else
            {
                $temp_arr = array(
                    'category_name' => __('admin.topCategory'),
                    'category_en_name' => __('admin.topCategory')
                );
                $this->ViewData['category_info']->parent_info = $temp_arr;
            }
        }

        return view('admin.article_category_edit',$this->ViewData);
    }

    /**
     * 文章分类 Ajax新增与编辑 提交处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function CategoryEditSubmit(Request $request)
    {
        /*初始化*/
        $admin_u  = session('AdminUser');
        $article  = new Article();
        $m3result = new M3Result();

        if($request->input('category_id') == 0)/*新增文章分类*/
        {
            /*验证规则*/
            $rules = [
                'category_id'  => 'required',
                'category_name'   => 'required',
                'category_sort'   => 'required|integer',
                'parent_id'   => 'required|integer',
            ];
            $validator = Validator::make($request->all(), $rules);

            /*按条件增加规则*/
            $validator->sometimes('parent_id', 'exists:article_category,category_id', function ($input) {
                return $input->parent_id !=0;/*return true时才增加验证规则!*/
            });

            if($validator->passes() && $article->addArticleCategory($request))
            {   /*验证通过并且添加成功*/
                $m3result->code    = 0;
                $m3result->messages= __('admin.success');
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
                $m3result->data['validator']    = $validator->messages();
                $m3result->data['article']      = $article->messages();
            }
        }
        else if($request->input('category_id') > 0)/*编辑文章分类*/
        {
            /*验证规则*/
            $rules = [
                'category_id'  => 'required',
                'category_name'   => 'required',
                'category_sort'   => 'required|integer',
                'parent_id'   => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $rules);

            /*按条件增加规则*/
            $validator->sometimes('parent_id', 'exists:article_category,category_id', function ($input) {
                return $input->parent_id !=0;/*return true时才增加验证规则!*/
            });

            if($validator->passes() && $article->editArticleCategory($request))
            {   /*验证通过并且更新成功*/
                $m3result->code    = 0;
                $m3result->messages= __('admin.success');
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
                $m3result->data['validator']    = $validator->messages();
                $m3result->data['article']      = $article->messages();
            }
        }
        else
        {
            $m3result->code    = 2;
            $m3result->messages= '无效数据';
        }

        return $m3result->toJson();
    }

    /**
     * 文章分类 Ajax删除提交
     * @param Request $request
     * @return \App\Tools\json
     */
    public function CategoryDeleteOne(Request $request)
    {
        /*初始化*/
        $article  = new Article();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'category_id'     => [
                'required',
                'integer',
                Rule::exists('article_category')->where(function ($query) {
                    $query->where('category_id',$GLOBALS['request']->input('category_id'));
                }),
            ]
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->passes() && $article->deleteArticleCategory($request->input('category_id')))
        {   /*验证通过*/
            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failed');
            $m3result->data['validator']    = $validator->messages();
            $m3result->data['article']      = $article->messages();
            if($m3result->data['article']['code'] == 1)
            {
                $m3result->code    = 2;
                $m3result->messages= $m3result->data['article']['messages'];
            }
            if($m3result->data['article']['code'] == 2)
            {
                $m3result->code    = 3;
                $m3result->messages= $m3result->data['article']['messages'];
            }
        }

        return $m3result->toJson();
    }

    /**
     * 文章列表页面(传如$category_id显示该分类下的所有文章)
     * @param int $category_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function InfoIndex($category_id = 0)
    {
        /*初始化*/
        $article  = new Article();
        $this->ViewData['article_list'] = null;
        $this->ViewData['category_tree'] = $article->getArticleCategoryTree();
        $this->ViewData['nav_position'] = Menu::getAdminPosition();/*面包屑*/
        if($category_id > 0)
        {
            $this->ViewData['category_info'] = $article->getOneCategoryRelationArticle($category_id);
            $this->ViewData['article_list'] = $article->getArticleList([['category_id',$category_id]]);
        }
        else
        {
            $this->ViewData['article_list'] = $article->getArticleList();
        }

        return view('admin.article_info_index',$this->ViewData);
    }

    /**
     * 文章编辑与新增页面
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function InfoView($id = 0)
    {
        /*初始化*/
        $article = new Article();
        $this->ViewData['article_info'] = null;
        $this->ViewData['category_tree'] = $article->getArticleCategoryTree();

        if($id > 0)
        {   /*编辑数据*/
            $this->ViewData['article_info']  = $article->getOneArticleInfoRelationCategory($id);
        }

        return view('admin.article_info_edit',$this->ViewData);
    }

    /**
     * 文章审核 Ajax提交
     * @param Request $request
     * @return \App\Tools\json
     */
    public function InfoAudit(Request $request)
    {
        /*初始化*/
        $article  = new Article();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'article_id'  => [
                'required',
                'integer',
                Rule::exists('article_info')->where(function ($query) {
                    $query->where('article_id',$GLOBALS['request']->input('article_id'));
                }),
            ],
            'audit_status' => [
                'required',
                Rule::in([Article::SUCCESS_AUDIT,Article::AWAIT_AUDIT]),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);

        if($validator->passes() & $article->auditArticleInfo($request->input('article_id'),$request->input('audit_status')))
        {   /*验证通过并且更新成功*/
            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failed');
            $m3result->data['validator']  = $validator->messages();
            $m3result->data['article']      = $article->messages();
        }
        return $m3result->toJson();
    }

    /**
     * 文章 Ajax删除提交
     * @param Request $request
     * @return \App\Tools\json
     */
    public function InfoDeleteOne(Request $request)
    {
        /*初始化*/
        $article  = new Article();
        $m3result = new M3Result();

        /*验证规则*/
        $rules = [
            'article_id'    => [
                'required',
                'integer',
                Rule::exists('article_info')->where(function ($query) {
                    $query->where('article_id',$GLOBALS['request']->input('article_id'));
                }),
            ]
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->passes() && $article->deleteArticleInfo($request->input('article_id')))
        {   /*验证通过*/
            $m3result->code    = 0;
            $m3result->messages= __('admin.success');
        }
        else
        {
            $m3result->code    = 1;
            $m3result->messages= __('admin.failed');
            $m3result->data['validator']    = $validator->messages();
            $m3result->data['article']      = $article->messages();
        }
        return $m3result->toJson();
    }

    /**
     * 文章 Ajax新增与编辑 提交处理
     * @param Request $request
     * @return \App\Tools\json
     */
    public function InfoEditSubmit(Request $request)
    {
        /*初始化*/
        $article  = new Article();
        $m3result = new M3Result();

        if($request->input('article_id') == 0)/*新增文章*/
        {
            /*验证规则*/
            $rules = [
                'article_id'  => 'required|integer',
                'category_id'   => [
                    'required',
                    'integer',
                    Rule::exists('article_category')->where(function ($query) {
                        $query->where('category_id',$GLOBALS['request']->input('category_id'));
                    }),
                ],
            ];
            $validator = Validator::make($request->all(), $rules);

            /*按条件增加规则*/
            $validator->sometimes('article_thumb', 'file|image', function ($input) use($request) {
                return $request->hasFile('article_thumb');/*return true时才增加验证规则!*/
            });
            $validator->sometimes('article_sort', 'integer', function ($input) use($request) {
                return $request->has('article_sort');/*return true时才增加验证规则!*/
            });
            $validator->sometimes('browse_count', 'integer', function ($input) use($request) {
                return $request->has('browse_count');/*return true时才增加验证规则!*/
            });

            if($validator->passes() && $article->addArticleInfo($request))
            {   /*验证通过并且添加成功*/
                $m3result->code    = 0;
                $m3result->messages= __('admin.success');
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
                $m3result->data['validator']    = $validator->messages();
                $m3result->data['article']      = $article->messages();
            }
        }
        else if($request->input('article_id') > 0)/*编辑文章*/
        {
            /*验证规则*/
            $rules = [
                'article_id'  => [
                    'required',
                    'integer',
                    Rule::exists('article_info')->where(function ($query) {
                        $query->where('article_id',$GLOBALS['request']->input('article_id'));
                    }),
                ],
                'category_id'   => [
                    'required',
                    'integer',
                    Rule::exists('article_category')->where(function ($query) {
                        $query->where('category_id',$GLOBALS['request']->input('category_id'));
                    }),
                ],
            ];
            $validator = Validator::make($request->all(), $rules);

            /*按条件增加规则*/
            $validator->sometimes('article_thumb', 'file|image', function ($input) use($request) {
                return $request->hasFile('article_thumb');/*return true时才增加验证规则!*/
            });
            $validator->sometimes('article_sort', 'integer', function ($input) use($request) {
                return $request->has('article_sort');/*return true时才增加验证规则!*/
            });
            $validator->sometimes('browse_count', 'integer', function ($input) use($request) {
                return $request->has('browse_count');/*return true时才增加验证规则!*/
            });

            if($validator->passes() & $article->editArticleInfo($request))
            {   /*验证通过并且更新成功*/
                $m3result->code    = 0;
                $m3result->messages= __('admin.success');
            }
            else
            {
                $m3result->code    = 1;
                $m3result->messages= __('admin.failed');
                $m3result->data['validator']  = $validator->messages();
                $m3result->data['article']      = $article->messages();
            }
        }
        else
        {
            $m3result->code    = 2;
            $m3result->messages= '无效数据';
        }


        return $m3result->toJson();

    }


}