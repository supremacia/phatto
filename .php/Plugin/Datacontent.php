<?php

namespace Plugin;

class Datacontent
{

    function __construct()
    {
        // ...constructor
    }

    public function render($data)
    {
        /*


            This is a sample content.
            In "real life", it should come from a database or similar. A dynamic content.


         */

        $data['tag'] = 'ul';
        $data['-content-'] = '<li>
			                    <div class="collapsible-header active"><i class="material-icons">cloud_download</i>Fully compatible with composer</div>
			                    <div class="collapsible-body">
			                        <p>Install your favorite libraries with all the ease that Composer offers. You can use the library components already included in PHATTO or switch to components, parts, or a complete framework, such as Laravel, CodeIgniter
			                            and others.</p>
			                        <p>The structure also includes the creation, export and installation of Plugins in the format adopted by Composer, facilitating the reuse of code and creation of universal components.</p>
			                        <p>Despite all compatibility with Composer, it is possible to run PHATTO without using Composer.</p>
			                    </div>
			                </li>
			                <li>
			                    <div class="collapsible-header"><i class="material-icons">directions</i>Solid Router</div>
			                    <div class="collapsible-body">
			                        <p>Complete, solid and extremely easy to use!</p>
			                        <p>The Router that comes with PHATTO is ready to go with you, from small to large projects.</p>
			                    </div>
			                </li>
			                <li>
			                    <div class="collapsible-header"><i class="material-icons">devices_other</i>View Library</div>
			                    <div class="collapsible-body">
			                        <p>The view control library is simple, fast, and low learning curve. Perfect for medium and small projects, it also has <b>NeosTags&copy;</b> - innovative and fluid template bounding!</p>
			                        <p><b>NeosTags&copy;</b> use configuration very similar to HTML tags, facilitating the use and learning even of those who do not have knowledge of PHP programming. The front designers of your team will love it !! <i class="material-icons">loyalty</i></p>
			                        <p>The system can still be configured to work with conventional MVC or MVC.</p>
			                        <p>Create your own plugins and components easily with <b>NeosTags&copy;</b>, easily exporting them to other projects.</p>
			                    </div>
			                </li>
			                <li>
			                    <div class="collapsible-header"><i class="material-icons">sd_card</i>Simple Database Object</div>
			                    <div class="collapsible-body">
			                        <p>Simple and intuitive object (class) for connection to SQL database (Mysql & SQLite, only).</p>
			                        <p>Build your queries and run in a few steps, getting data on an active, recyclable "ROW" object.</p>
			                        <p>In the current version we recommend its use in small projects only. For more functionality you can use a more robust component (library or system) such as Laravel Eloquent, MongoDB and others of your choice.</p>
			                    </div>
			                </li>';
        return \Lib\NTag::this()->setAttributes($data);
    }
}
