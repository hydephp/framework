---
priority: 25
label: "Compiling to HTML"
category: "Creating Content"
---

# Compiling to static HTML

## Running the build command

Now that you have some amazing content, you'll want to compile your site into static HTML.

**This is as easy as executing the `build` command:**
```bash
php hyde build
```

**You can also compile a single file:**
```bash
php hyde rebuild <filepath>
```

**And, you can even start a development server to compile your site on the fly:**
```bash
php hyde serve
```

**Learn more about these commands in the [console commands](console-commands) documentation:**

- [Build command](console-commands#build-the-static-site)
- [Rebuild command](console-commands#build-a-single-file)
- [Serve command](console-commands#start-the-realtime-compiler)


## Concepts

### Autodiscovery
When building the site, Hyde will your source directories for files and compile them into static HTML using the appropriate layout depending on what kind of page it is. You don't have to worry about routing as Hyde takes care of that, including creating navigation menus!