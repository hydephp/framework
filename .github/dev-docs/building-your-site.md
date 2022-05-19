---
priority: 25
label: "Compiling to HTML"
category: "Creating Content"
---

# Compiling to static HTML

## Running the build command

Now that you have some amazing content, you'll want to compile your site into static HTML.

This is as easy as executing the `build` command:
```bash
php hyde build
```

> Learn more about the options supported in the [console commands](console-commands#build-the-static-site) section.

## More ways to build your site

### Previewing your site live

It can quickly become tedious to have to hop to your terminal to rebuild your site.

The Hyde Realtime Compiler solves this problem by starting a development server that 
automatically compiles your site on the fly. Start the server using the `serve` command:

```bash
php hyde serve          
```

> Learn more in the [console commands](console-commands#start-the-realtime-compiler) section.

### Compiling a single file

Using the php hyde build command is great and all that, but when you just need to update that one file
it gets a little... overkill. To solve this problem, you can use the rebuild command to compile a single file:

```bash
php hyde rebuild <filepath>
```
> Learn more in the [console commands](console-commands#build-a-single-file) section.


## Concepts

### Autodiscovery
When building the site, Hyde will your source directories for files and compile them into static HTML using the appropriate layout depending on what kind of page it is. You don't have to worry about routing as Hyde takes care of that, including creating navigation menus!