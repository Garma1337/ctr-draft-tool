<nav aria-label="Pagination">
    <ul class="pagination">
        {if $page > 1}
            <li class="page-item">
                <a class="page-link" href="{$router->generateUrl('draftList', ['page' => ($page - 1)])}">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        {else}
            <li class="page-item disabled">
                <a class="page-link" href="#">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        {/if}

        {for $i=1 to $pages step 1}
            <li class="page-item{if $i === $page} active{/if}">
                <a class="page-link" href="{$router->generateUrl('draftList', ['page' => $i])}">{$i}</a>
            </li>
        {/for}

        {if ($page + 1) <= $pages}
            <li class="page-item">
                <a class="page-link" href="{$router->generateUrl('draftList', ['page' => ($page + 1)])}">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        {else}
            <li class="page-item disabled">
                <a class="page-link" href="#">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        {/if}
    </ul>
</nav>