=== Simple Markdown ===
Contributors: Blokkie
Tags: markdown, gutenberg, blocks, content, formatting, code, beautification
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Simple and fast plugin to render markdown with a custom Gutenberg block. Professional code beautification and copy functionality included.

== Description ==

Simple Markdown is a lightweight WordPress plugin that adds a custom Gutenberg block for rendering Markdown content directly in your posts and pages. Perfect for developers, writers, and anyone who prefers writing in Markdown format.

**NEW in 1.3.0: Professional Code Beautification**
* Automatic code formatting and indentation for all supported languages
* Professional copy buttons positioned as elegant tabs outside code blocks
* Language-aware beautification (JavaScript, PHP, CSS, JSON, Bash)
* Smart preservation of ASCII art and tree structures in copy-only blocks

**Core Features:**

* Custom Gutenberg block for Markdown content
* Support for common Markdown syntax:
  * Headers (H1-H6)
  * Bold and italic text
  * Inline code and code blocks with copy functionality
  * Links
  * Unordered and ordered lists
  * Blockquotes
* Professional code block rendering with:
  * Automatic beautification and proper indentation
  * One-click copy functionality with visual feedback
  * Language labels (JavaScript, PHP, CSS, JSON, Bash)
  * Tight line spacing optimized for code readability
* Clean HTML output with semantic markup
* Theme-resistant styling with robust CSS overrides
* Easy to use interface

This is the core version with all essential markdown features plus professional code handling. A Pro version with syntax highlighting and other advanced features will be available in the future.

**Supported Markdown Syntax:**

* `# Header 1` through `###### Header 6`
* `**bold text**` and `*italic text*`
* `` `inline code` `` and code blocks with triple backticks
* `[link text](URL)` for links
* `- item` for unordered lists
* `1. item` for ordered lists
* `> quote` for blockquotes

**Advanced Code Block Syntax:**

* `` ```javascript copy `` - Beautified JavaScript with copy button
* `` ```php copy `` - Beautified PHP with copy button
* `` ```css copy `` - Beautified CSS with copy button
* `` ```json copy `` - Beautified JSON with copy button
* `` ```bash copy `` - Bash commands with copy button
* `` ```copy `` - Copy button without beautification (preserves ASCII art)
* `` ``` `` - Plain code block (no beautification, no copy button)

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. In the Gutenberg editor, look for the "Markdown" block in the block inserter.
4. Add your Markdown content and it will be rendered as HTML on the frontend.
5. Use advanced code block syntax like `` ```javascript copy `` for beautified code with copy functionality.

== Frequently Asked Questions ==

= Does this plugin support all Markdown features? =

This plugin supports the most commonly used Markdown features including headers, emphasis, links, lists, code blocks with beautification, and blockquotes. It's designed to be lightweight and fast while providing professional code handling.

= How does the code beautification work? =

Code blocks with language tags (like ```javascript copy) are automatically beautified with proper indentation and formatting. Copy-only blocks (```copy) preserve exact formatting for ASCII art and tree structures.

= Can I use this with the Classic Editor? =

This plugin is designed specifically for the Gutenberg block editor. For Classic Editor support, consider using a different Markdown solution.

= Does this plugin require any external libraries? =

No, Simple Markdown uses a custom, lightweight Markdown parser with built-in beautification algorithms, requiring no external dependencies.

= What's the difference between language+copy and copy-only blocks? =

Language+copy (```javascript copy) beautifies code AND adds copy button. Copy-only (```copy) adds copy button WITHOUT beautification, preserving ASCII art and special formatting.

= Will there be a Pro version? =

Yes, a Pro version with syntax highlighting and advanced features is planned for the future. The current version includes professional code beautification and copy functionality.

== Changelog ==

= 1.3.0 =
* MAJOR FEATURE: Professional code beautification system with language-aware formatting
* NEW: Advanced copy functionality with elegant tab-style buttons positioned outside code blocks
* NEW: Language-specific auto-beautification for JavaScript, PHP, CSS, JSON, and Bash
* NEW: Smart preservation system - copy-only blocks (```copy) maintain exact formatting for ASCII art
* NEW: Three-tier code block processing: beautified language blocks, copy-only preservation, plain blocks
* Enhanced: Copy buttons now positioned as professional tabs matching language tag styling
* Enhanced: Automatic multi-line formatting with proper indentation for compressed code
* Enhanced: One-click copy functionality with visual feedback ("copy" → "copied!" → "copy")
* Enhanced: Robust theme-resistant CSS with improved code block styling
* Enhanced: Strategic line break insertion for better code readability
* Technical: Complete rewrite of code block processing with placeholder protection system
* Technical: Language-aware beautification algorithms for each supported syntax
* Perfect foundation for Pro version with syntax highlighting capabilities
* Maintains ultra-lightweight approach with zero external dependencies

= 1.1.2 =
* Extreme ultra-tight line spacing (0.4) for code blocks - maximum density without text overlap
* Perfect for ASCII art, tree structures, and dense code examples

= 1.1.1 =
* Ultra-minimal line spacing (1.05) in code blocks - lines as close as possible without overlap
* Standard paragraph font size (1em) for readability
* Perfect for tree structures, code examples, and technical documentation

= 1.1.0 =
* Major improvement: Enhanced code block rendering with professional tight line spacing
* Added robust CSS overrides to prevent WordPress theme interference
* Implemented proper whitespace handling for code blocks
* Significantly improved code readability and appearance

= 1.0.2 =
* Fixed code block line spacing for better alignment
* Reduced line height in code blocks from 1.4 to 1.2 for tighter spacing

= 1.0.1 =
* Fixed markdown rendering font sizes and line spacing
* Improved typography for better readability
* Better spacing between elements

= 1.0.0 =
* Initial release
* Custom Gutenberg block for Markdown rendering
* Support for headers, emphasis, links, lists, code blocks, and blockquotes
* Clean HTML output with proper semantic markup

== Upgrade Notice ==

= 1.3.0 =
MAJOR FEATURE RELEASE: Professional code beautification system with language-aware formatting, elegant copy buttons positioned as tabs, and smart preservation for ASCII art. Complete rewrite of code processing with multi-language support. Essential upgrade for developers and technical writers.

= 1.1.2 =
Extreme density: 0.4 line-height achieves maximum possible line density in code blocks without text overlap - ideal for complex ASCII structures.

= 1.1.1 =
Ultra-tight spacing: 1.05 line-height brings lines as close together as possible without overlap - perfect for code blocks and ASCII art.

= 1.1.0 =
Major release: Dramatically improved code block rendering with professional tight spacing and theme compatibility. Highly recommended upgrade for better code display.

= 1.0.2 =
Fixed code block line spacing for tighter, more readable code formatting.

= 1.0.1 =
Improved markdown rendering with better font sizes and spacing.

= 1.0.0 =
Initial release of Simple Markdown.
