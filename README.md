This project has been discontinued. Check out our next project: a [4G security camera](https://sensorable.io).

# Productivity Extension


The Productivity extension contains a number of productivity-related features to make the everyday store administration tasks easier. It includes one-click switching between the front- and back-end views of products, categories and CMS pages, a front-end toolbar for simple product editing without back-end access and a number of useful widgets.

## Front-end features

### Productivity Toolbar

The toolbar is shown at the top of Product, Category and CMS pages in the front-end for all _authorised_ users. It contains a number of buttons that give access to the following functionality:

* Edit product details, prices, attributes, etc.
* Upload product images.
* List products with missing images.
* Shortcut to the corresponding page (product, category or CMS page) in the back-end.
* Shortcut to Google Analytics.
* Help on using the extension.

**Note:**
  
   1. Only customers who are members of the _Reviewer_ group will get access to the front-end toolbar.     
   2. Shortcuts to the admin part of the website are only enabled if the user is currently logged in to the back-end with an administrative account in the same browser.
	

### Image Editing Toolbar

The toolbar is shown when the mouse pointer is hovering over a product image (main image or a thumbnail) in the Product page. It allows simple image editing functionality:
* Delete and rotate product images
* Set main product image

**Note:**

   Image editing toolbar may not always work in custom themes (with modified HTML layout or product image sizes) and may require some additional configuration, as outlined in XXX.

### Expanding hidden category

It's possible to replace hidden category with its child categories in the menu using _Flatten categories tree_ setting. To archive it follow instructions:

1. Set _Flatten categories tree_ setting in the productivity settings to _Show children of hidden parents_
2. Set _Use Include in Navigation Menu_ setting in the desired category's settings to _No_

The category will be replaced with its child categories in the menu or will be hidden if it doesn't have children

## Back-end features

#### Back-end to Front-end shortcuts

_FrontShop Preview_ button is added to Product editing, Category editing and CMS page editing views to allow one-click preview in the front-end.

#### Open in a new window

Use middle button on your mouse in lists to open the item in a new window.
E.g. list of attributes in Attribute Set page, list of CMS pages, etc.



## Other features
### RSS Feeds
* List of latest products (e.g. http://try.mventory.com/productivity_admin/rss_product/latest/)
* List of products per category (e.g. http://try.mventory.com/books.html?rss=1)
* List of top categories per store (e.g. http://try.mventory.com/catalog/category/top/)
 
### Widgets
* Slideshow widget
* Attribute values widget
* Related products widget

### Blocks
* Related products block
	


## Theme compatibility
Most well designed themes are compatible with this extension. You may need to make a few minor changes, e.g. to configure the Image Editing Toolbar.


## Configuration
The extension doesn't require any initial configuration. The front-end toolbar is displayed on the front end for all authorised users.

#### User configuration

1. Create a user group called `Reviewer`
2. Assign an active user to that group to give them access to the productivity toolbar.

The user should see the toolbar after logging it to the front-end.
