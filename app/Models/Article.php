<?php
/**
 * Created by LaravelShop.
 * Author: Walker  QQ:120007700
 * Date  : 2017/5/18 0018
 * Time  : 14:17
 */

namespace App\Models;
use App\Entity\ArticleCategory;
use App\Entity\ArticleInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class Article 文章相关的模型
 * @package App\Models
 */
class Article extends CommonModel{

    private $errors =array(); /*错误信息*/

    const AWAIT_AUDIT = 0; /*文章等待审核*/
    const SUCCESS_AUDIT = 1; /*文章审核通过*/

    /**
     * 添加一个文章分类
     * @param $request
     * @return bool
     */
    public function addArticleCategory($request)
    {
        /*初始化*/
        $article_category = new ArticleCategory();

        $article_category->parent_id = $request->input('parent_id');
        $article_category->category_name = $request->input('category_name');
        $article_category->category_en_name = $request->has('category_en_name') ? $request->input('category_en_name') : '';
        $article_category->category_sort = $request->input('category_sort');
        $article_category->save();
        Rbac::adminLog('新增文章分类:'.$article_category->category_name."($article_category->category_id)");

        return true;
    }

    /**
     * 更新一个文章分类
     * @param $request
     * @return bool
     */
    public function editArticleCategory($request)
    {
        /*初始化*/
        $article_category = new ArticleCategory();
        $edit_category    = $article_category->findOrFail($request->input('category_id'));
        $name             = $edit_category->category_name;

        $edit_category->parent_id = $request->input('parent_id');
        $edit_category->category_name = $request->input('category_name');
        $edit_category->category_en_name = $request->has('category_en_name') ? $request->input('category_en_name') : '';
        $edit_category->category_sort = $request->input('category_sort');
        $edit_category->save();
        Rbac::adminLog('编辑文章分类:'.$name."($edit_category->category_id)");
        return true;
    }

    /**
     * 删除一个文章分类  (如果该分类下有子分类或有文章则 return false)
     * @param $category_id
     * @return bool
     */
    public function deleteArticleCategory($category_id)
    {
        /*初始化*/
        $article_category = new ArticleCategory();
        $child_category  = null;

        $delete_category = $this->getOneCategoryRelationArticle($category_id);
        $child_category  = $article_category->where('parent_id',$category_id)->get();

        if(!$child_category->isEmpty())
        {
            $this->errors['code'] = 1;
            $this->errors['messages'] = __('admin.failed').',当前分类下存在下级分类';
            return false;
        }
        elseif(!$delete_category->article_info->isEmpty())
        {
            $this->errors['code'] = 2;
            $this->errors['messages'] = __('admin.failed').',当前分类下存在文章';
            return false;
        }
        else
        {
            $delete_category->delete();
            Rbac::adminLog('删除文章分类:'.$delete_category->category_name."($delete_category->category_id)");
            return true;
        }
    }

    /**
     * 添加一篇文章
     * @param $request
     * @return bool
     */
    public function addArticleInfo($request)
    {
        /*初始化*/
        $article_info = new ArticleInfo();
        $my_file      = new MyFile();

        /*添加文章*/
        $article_info->category_id = $request->input('category_id');/*分类id*/
        $article_info->audit_status = self::AWAIT_AUDIT;/*审核状态*/
        $article_info->article_title = $request->has('article_title')  ? $request->input('article_title') : '';/*中文标题*/
        $article_info->article_en_title = $request->has('article_en_title')  ? $request->input('article_en_title') : '';/*英文标题*/
        $article_info->article_content = $request->has('article_content')  ? $request->input('article_content') : '';/*中文内容*/
        $article_info->article_en_content = $request->has('article_en_content')  ? $request->input('article_en_content') : '';/*英文内容*/
        $article_info->article_author = $request->has('article_author')  ? $request->input('article_author') : '';/*文章作者*/
        $article_info->article_keywords = $request->has('article_keywords')  ? $request->input('article_keywords') : '';/*文章关键字*/
        $article_info->article_sort = $request->has('article_sort')  ? $request->input('article_sort') : 1000;/*文章排序*/
        $article_info->browse_count = $request->input('browse_count') != 0  ?  $request->input('browse_count') : mt_rand(1,10);/*文章浏览次数*/

        /*文章缩略图*/
        if($request->hasFile('article_thumb'))
        {
            $article_info->article_thumb = $my_file->uploadThumb($request->file('article_thumb'));/*上传成功*/
        }
        else
        {
            $article_info->article_thumb = $GLOBALS['shop_config']['shop_default_picture'];/*默认图片*/
        }

        $article_info->save();
        Rbac::adminLog('新增文章:'.$article_info->article_title."($article_info->article_id)");
        return true;
    }

    /**
     * 编辑一篇文章
     * @param $request
     * @return bool
     */
    public function editArticleInfo($request)
    {
        /*初始化*/
        $article_info = new ArticleInfo();
        $my_file      = new MyFile();
        $edit_article = $article_info->findOrFail($request->input('article_id'));

        /*编辑文章*/
        $edit_article->category_id = $request->input('category_id');/*分类id*/
        $edit_article->article_title = $request->has('article_title')  ? $request->input('article_title') : '';/*中文标题*/
        $edit_article->article_en_title = $request->has('article_en_title')  ? $request->input('article_en_title') : '';/*英文标题*/
        $edit_article->article_content = $request->has('article_content')  ? $request->input('article_content') : '';/*中文内容*/
        $edit_article->article_en_content = $request->has('article_en_content')  ? $request->input('article_en_content') : '';/*英文内容*/
        $edit_article->article_author = $request->has('article_author')  ? $request->input('article_author') : '';/*文章作者*/
        $edit_article->article_keywords = $request->has('article_keywords')  ? $request->input('article_keywords') : '';/*文章关键字*/
        $edit_article->article_sort = $request->has('article_sort')  ? $request->input('article_sort') : 1000;/*文章排序*/
        $edit_article->browse_count = $request->has('browse_count')  ?  $request->input('browse_count') : mt_rand(1,10);/*文章浏览次数*/

        /*文章缩略图*/
        if($request->hasFile('article_thumb'))
        {
            $edit_article->article_thumb = $my_file->uploadThumb($request->file('article_thumb'));/*上传成功*/
        }

        $edit_article->save();
        Rbac::adminLog('编辑文章:'.$edit_article->article_title."($edit_article->article_id)");
        return true;
    }


    /**
     * 更新一篇文章的审核状态
     * @param $article_id & 文章id
     * @param null $status & 审核状态
     * @return bool
     */
    public function auditArticleInfo($article_id , $status = null)
    {
        /*初始化*/
        $article_info = new ArticleInfo();

        if($article_id > 0 && in_array($status,[self::AWAIT_AUDIT,self::SUCCESS_AUDIT]))
        {
            $edit_article = $article_info->findOrFail($article_id);
            $edit_article->audit_status = $status;
            if($status == self::AWAIT_AUDIT)
            {
                Rbac::adminLog('文章下架:'.$edit_article->article_title."($edit_article->article_id)");
            }
            elseif($status == self::SUCCESS_AUDIT)
            {
                $edit_article->created_at = Carbon::now()->timestamp;
                Rbac::adminLog('文章发布:'.$edit_article->article_title."($edit_article->article_id)");
            }
            $edit_article->save();
            return true;
        }
        else
        {
            $this->errors['code'] = 1;
            $this->errors['messages'] = '非法参数';
            return false;
        }
    }

    /**
     * 删除一遍文章
     * @param $article_id
     * @return bool
     */
    public function deleteArticleInfo($article_id)
    {
        /*初始化*/
        $article_info = ArticleInfo::findOrFail($article_id);

        $article_info->delete();
        Rbac::adminLog('删除文章:'.$article_info->article_title."($article_info->article_id)");
        return true;
    }

    /**
     * 获取所有文章列表关联分类(如有where 则加入新的sql条件)"分页,语言"
     * @param null $where = [['audit_status',$article::AWAIT_AUDIT],['category_id',151],]
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|null
     */
    public function getArticleList($where = null)
    {
        /*初始化*/
        $article_info = new ArticleInfo();
        $article_list = null;

        /*预加载ORM对象*/
        if(!empty($where) && is_array($where))
        {
            $article_list = $article_info->with('ho_article_category')->orderBy('article_sort','asc')->where($where)->paginate($_COOKIE['AdminPaginationSize']);
        }
        else
        {
            $article_list = $article_info->with('ho_article_category')->orderBy('article_sort','asc')->paginate($_COOKIE['AdminPaginationSize']);
        }


        /*数据过滤排版*/
        $article_list->transform(function($item)
        {
            $item->title   = CommonModel::languageFormat($item->article_title,$item->article_en_title);
            $item->article_category = $item->ho_article_category;
            $item->article_thumb = MyFile::makeUrl($item->article_thumb);
            $item->article_category->name = CommonModel::languageFormat($item->article_category->category_name ,$item->article_category->category_en_name );
            return $item;

        });

        return $article_list;
    }

    /**
     * 获取所有文章分类,用于zTree的未分级格式 (已转换中英文,文章数量统计)
     * @return mixed
     */
    public function getArticleCategoryTree()
    {
        /*初始化*/
        $article_category = new ArticleCategory();

        $data = $article_category->withCount('hm_article_info')->orderBy('category_sort', 'asc')->get();

        $data->transform(function($item)
        {
            $item->article_count = $item->hm_article_info_count;
            $item->name = CommonModel::languageFormat($item->category_name , $item->category_en_name);
            return $item;
        });

        return $data;
    }

    /**
     * 获取单个文章详情与对应分类的关联数据
     * @param $article_id
     * @return mixed
     */
    public function getOneArticleInfoRelationCategory($article_id)
    {
        /*初始化*/
        $article_info = new ArticleInfo();

        /*查询*/
        $data = $article_info->findOrFail($article_id);

        /*数据过滤*/
        $data->article_category = $data->ho_article_category;
        $data->article_thumb = MyFile::makeUrl($data->article_thumb);

        return $data;
    }

    /**
     * 获取单个文章分类数据关联分类下文章(转换中英文)
     * @param null $category_id
     * @return  mixed & bool
     */
    public function getOneCategoryRelationArticle($category_id = null)
    {
        /*初始化*/
        $article_category = new ArticleCategory();

        if($category_id)
        {
            /*查询*/
            $data = $article_category->findOrFail($category_id);
            /*数据过滤*/
            $data->name = CommonModel::languageFormat($data->category_name,$data->category_en_name);
            $data->article_info = $data->hm_article_info;
            return $data;
        }
        else
        {
            return false;
        }
    }

    /**
     * 返回 模型 发生的错误信息
     * @return mixed
     */
    public function messages()
    {
        return $this->errors;
    }
}