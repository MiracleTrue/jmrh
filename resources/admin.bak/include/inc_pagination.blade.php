<div class="adminPagination cl">
    <span class="pagination_size">{{__('admin.paginationShow')}}<input onchange="document.cookie='AdminPaginationSize=' + this.value;location.replace(location.href);" type="text" value="{{$_COOKIE['AdminPaginationSize'] or 15}}" /></span>
    {{ $pagination->links() }}
</div>