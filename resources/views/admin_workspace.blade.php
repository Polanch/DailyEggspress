@extends('layouts.admin_layout')

@section('content')
    <div class="workspace">
        <div class="workspace-header">
            <h1 class="admin-header"><img src="/images/menu1.png" class="admin-h-icn">Dashboard<span class="slash">/</span><span id="hh">Workspace</span></h1>
            <h3 class="admin-subheader">Workspace</h3>
        </div>
        <div class="workspace-body">
            <div class="tool-header">
                <div class="search-bar">
                    <img src="/images/search.png" class="admin-h-icn">
                    <input type="text" placeholder="Search">
                </div>
                <a href="/admin/workspace/create" class="create-blog-btn">Create Blog</a>
            </div>
            <div class="tool-subheader">
                <div class="t-box">
                    <button class="t-option active" data-tab="all">All (40)</button>
                    <button class="t-option" data-tab="drafts">Drafts(5)</button>
                    <button class="t-option" data-tab="scheduled">Scheduled(2)</button>
                    <button class="t-option" data-tab="published">Published(30)</button>
                    <button class="t-option" data-tab="trash">Trash(0)</button>
                </div>
                
                <div class="filter-box">
                    <select name="" id="filter-select">
                        <option value="">Newest</option>
                        <option value="">Oldest</option>
                    </select>
                    <div class="f-box">
                        <button><img src="/images/f1.png" class="admin-h-icn" id="list-style-btn"></button>
                        <button><img src="/images/f2.png" class="admin-h-icn" id="grid-style-btn"></button>
                    </div>
                    <div class="f-box">
                        <button><img src="/images/f3.png" class="admin-h-icn" id="header-previous-btn"></button>
                        <button><img src="/images/f4.png" class="admin-h-icn" id="header-next-btn"></button>
                    </div>
                </div>
            </div>
            <div class="tool-body">
                <div class="tool-table">
                    <table>
                        <thead>
                            <tr>
                                <th colspan="2">Title</th>
                                <th>Author</th>
                                <th>Publish Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Lorem ipsum dolor sit amet consectetur adipisicing.</td>
                                <td><span class="status-badge status-draft">Draft</span></td>
                                <td>John Lloyd Olipani</td>
                                <td>-</td>
                                <td class="t-actions">
                                    <button class="action-edit">Edit</button>
                                    <button class="action-delete" title="Delete"><img src="/images/trash.png" alt="Delete" class="trash-icn"></button>
                                </td>
                            </tr>
                            <tr>
                                <td>Lorem ipsum dolor sit amet consectetur adipisicing.</td>
                                <td><span class="status-badge status-draft">Draft</span></td>
                                <td>John Lloyd Olipani</td>
                                <td>-</td>
                                <td class="t-actions">
                                    <button class="action-edit">Edit</button>
                                    <button class="action-delete" title="Delete"><img src="/images/trash.png" alt="Delete" class="trash-icn"></button>
                                </td>
                            </tr>
                            <tr>
                                <td>Placeholder Title 2</td>
                                <td><span class="status-badge status-scheduled">Scheduled</span></td>
                                <td>John Lloyd Olipani</td>
                                <td>2026-02-25 10:00</td>
                                <td class="t-actions">
                                    <button class="action-edit">Edit</button>
                                    <button class="action-delete" title="Delete"><img src="/images/trash.png" alt="Delete" class="trash-icn"></button>
                                </td>
                            </tr>
                            <tr>
                                <td>Placeholder Title 3</td>
                                <td><span class="status-badge status-published">Published</span></td>
                                <td>John Lloyd Olipani</td>
                                <td>2026-02-18</td>
                                <td class="t-actions">
                                    <button class="action-edit">Edit</button>
                                    <button class="action-delete" title="Delete"><img src="/images/trash.png" alt="Delete" class="trash-icn"></button>
                                </td>
                            </tr>
                            <tr>
                                <td>Placeholder Title 4</td>
                                <td><span class="status-badge status-trash">Trash</span></td>
                                <td>John Lloyd Olipani</td>
                                <td>-</td>
                                <td class="t-actions">
                                    <button class="action-edit">Edit</button>
                                    <button class="action-delete" title="Delete"><img src="/images/trash.png" alt="Delete" class="trash-icn"></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="table-footer">
                        <div class="tf-left">Showing 1-5 out of 10</div>
                        <div class="tf-right">
                            <button class="page-btn page-prev" title="Previous">&lt;</button>
                            <button class="page-btn page-next" title="Next">&gt;</button>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tool-footer"></div>
        </div>
    </div>
@endsection


