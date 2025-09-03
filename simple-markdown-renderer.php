<?php
/**
 * Plugin Name: Simple Markdown
 * Plugin URI: https://floris-jan.com/
 * Description: Simple and fast plugin to render markdown with a custom Gutenberg block. Add markdown content anywhere on your site with full control.
 * Version: 1.3.0
 * Author: Blokkie
 * Author URI: https://floris-jan.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class SimpleMarkdown {

    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
        add_action('enqueue_block_assets', array($this, 'enqueue_block_assets'));
    }

    public function init() {
        // Check if Pro version is active
        if ($this->is_pro_version_active()) {
            // Don't register our block if Pro is active
            return;
        }

        // Register the Gutenberg block
        register_block_type('simple-markdown/markdown-block', array(
            'render_callback' => array($this, 'render_markdown_block'),
            'attributes' => array(
                'content' => array(
                    'type' => 'string',
                    'default' => ''
                )
            )
        ));
    }

    /**
     * Check if Pro version is active
     */
    private function is_pro_version_active() {
        // Check if pro plugin class exists
        return class_exists('SimpleMarkdownPro') ||
               is_plugin_active('simple-markdown-pro/simple-markdown-pro.php') ||
               function_exists('simple_markdown_pro_init');
    }


    /**
     * Enqueue block assets (frontend and editor)
     */
    public function enqueue_block_assets() {
        wp_enqueue_style(
            'simple-markdown-style',
            plugin_dir_url(__FILE__) . 'style.css',
            array(),
            '1.3.0'
        );
        
        // Add inline JavaScript for copy functionality only (beautification now done in PHP)
        wp_register_script('simple-markdown-copy', '', array(), '1.3.0', true);
        wp_enqueue_script('simple-markdown-copy');
        wp_add_inline_script('simple-markdown-copy', '
        // Copy functionality
        function copyCodeBlock(button) {
            var container = button.closest(".code-block-wrapper");
            var code = container.getAttribute("data-code");
            if (navigator.clipboard) {
                navigator.clipboard.writeText(code).then(function() {
                    button.textContent = "copied!";
                    button.title = "Copied!";
                    setTimeout(function() {
                        button.textContent = "copy";
                        button.title = "Copy code";
                    }, 2000);
                }).catch(function() {
                    fallbackCopy(code, button);
                });
            } else {
                fallbackCopy(code, button);
            }
        }
        
        function fallbackCopy(text, button) {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand("copy");
                button.textContent = "copied!";
                button.title = "Copied!";
                setTimeout(function() {
                    button.textContent = "copy";
                    button.title = "Copy code";
                }, 2000);
            } catch (err) {
                console.error("Copy failed");
            }
            document.body.removeChild(textArea);
        }
        
        function runBeautification() {
            console.log("Simple Markdown: Starting code beautification");
            
            var codeBlocks = document.querySelectorAll(".code-block-wrapper pre code, pre code");
            console.log("Found " + codeBlocks.length + " code blocks");
            codeBlocks.forEach(function(block) {
                // Get the raw code content
                var code = block.textContent;
                console.log("Processing code block:", code.substring(0, 50) + "...");
                
                // Clean and professional code beautification
                var lines = code.split("\n");
                console.log("Split into " + lines.length + " lines");
                var beautifiedLines = [];
                var indentLevel = 0;
                
                lines.forEach(function(line) {
                    var trimmed = line.trim();
                    if (trimmed.length === 0) {
                        beautifiedLines.push("");
                        return;
                    }
                    
                    // Decrease indent for closing brackets/braces
                    if (trimmed.match(/^[}\])]/) || trimmed === "}" || trimmed === ")" || trimmed === "]") {
                        indentLevel = Math.max(0, indentLevel - 1);
                    }
                    
                    // Add properly indented line
                    beautifiedLines.push("  ".repeat(indentLevel) + trimmed);
                    
                    // Increase indent for opening brackets/braces  
                    if (trimmed.match(/[{\[(]\s*$/) || trimmed.endsWith("{") || trimmed.endsWith("(") || trimmed.endsWith("[")) {
                        indentLevel++;
                    }
                });
                
                // Only update if formatting improved the code
                var beautified = beautifiedLines.join("\n");
                if (beautified.trim() !== code.trim()) {
                    console.log("Beautifying code block - before:", code.substring(0, 50));
                    console.log("Beautifying code block - after:", beautified.substring(0, 50));
                    block.textContent = beautified.trim();
                } else {
                    console.log("No beautification needed for this block");
                }
            });
        }
        
        // Start beautification when DOM is ready
        document.addEventListener("DOMContentLoaded", runBeautification);
        ');
    }

    /**
     * Enqueue block editor assets
     */
    public function enqueue_block_editor_assets() {
        // Don't enqueue if Pro version is active
        if ($this->is_pro_version_active()) {
            return;
        }

        wp_enqueue_script(
            'simple-markdown-block',
            plugin_dir_url(__FILE__) . 'block.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
            '1.3.0',
            true
        );
    }

    /**
     * Beautify code content with proper indentation and language-specific formatting
     */
    private function beautify_code($code, $language = '') {
        if (empty(trim($code))) {
            return $code;
        }
        
        // First, convert single-line code to multi-line by adding strategic line breaks
        $code = $this->force_multiline_formatting($code, $language);
        
        $lines = explode("\n", $code);
        $beautified_lines = array();
        $indent_level = 0;
        
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (strlen($trimmed) === 0) {
                $beautified_lines[] = '';
                continue;
            }
            
            // Language-specific indentation rules
            $this->adjust_indent_level($trimmed, $indent_level, $language, 'before');
            
            // Add properly indented line
            $beautified_lines[] = str_repeat('  ', $indent_level) . $trimmed;
            
            // Adjust indent level for next line
            $this->adjust_indent_level($trimmed, $indent_level, $language, 'after');
        }
        
        $beautified = implode("\n", $beautified_lines);
        return trim($beautified);
    }
    
    /**
     * Force single-line code to multi-line with strategic breaks
     */
    private function force_multiline_formatting($code, $language) {
        // Remove extra whitespace and normalize
        $code = preg_replace('/\s+/', ' ', trim($code));
        
        // Language-specific multi-line formatting
        switch (strtolower($language)) {
            case 'javascript':
            case 'js':
                return $this->format_javascript($code);
            case 'php':
                return $this->format_php($code);
            case 'css':
                return $this->format_css($code);
            case 'json':
                return $this->format_json($code);
            case 'bash':
            case 'sh':
                return $this->format_bash($code);
            default:
                return $this->format_generic($code);
        }
    }
    
    /**
     * JavaScript-specific formatting
     */
    private function format_javascript($code) {
        // Add line breaks after common patterns
        $code = preg_replace('/([;{}])\s*/', "$1\n", $code);
        $code = preg_replace('/(\{)\s*/', "$1\n", $code);
        $code = preg_replace('/(\})\s*/', "\n$1\n", $code);
        return $code;
    }
    
    /**
     * PHP-specific formatting  
     */
    private function format_php($code) {
        // Add line breaks after common patterns
        $code = preg_replace('/([;{}])\s*/', "$1\n", $code);
        $code = preg_replace('/(\{)\s*/', "$1\n", $code);
        $code = preg_replace('/(\})\s*/', "\n$1\n", $code);
        return $code;
    }
    
    /**
     * CSS-specific formatting
     */
    private function format_css($code) {
        // Add line breaks for CSS rules
        $code = preg_replace('/([{}:;])\s*/', "$1\n", $code);
        $code = preg_replace('/(\{)\s*/', "$1\n", $code);
        $code = preg_replace('/(\})\s*/', "\n$1\n", $code);
        return $code;
    }
    
    /**
     * JSON-specific formatting
     */
    private function format_json($code) {
        // Add line breaks for JSON structure
        $code = preg_replace('/([,{}[\]])\s*/', "$1\n", $code);
        $code = preg_replace('/(\{|\[)\s*/', "$1\n", $code);
        $code = preg_replace('/(\}|\])\s*/', "\n$1\n", $code);
        return $code;
    }
    
    /**
     * Bash-specific formatting
     */
    private function format_bash($code) {
        // Split bash commands on common separators
        $code = preg_replace('/([;&|])\s*/', "$1\n", $code);
        return $code;
    }
    
    /**
     * Generic formatting for unknown languages
     */
    private function format_generic($code) {
        // Basic formatting for any code
        $code = preg_replace('/([;{}])\s*/', "$1\n", $code);
        $code = preg_replace('/(\{)\s*/', "$1\n", $code);
        $code = preg_replace('/(\})\s*/', "\n$1\n", $code);
        return $code;
    }
    
    /**
     * Adjust indentation level based on language-specific rules
     */
    private function adjust_indent_level($line, &$indent_level, $language, $phase) {
        if ($phase === 'before') {
            // Decrease indent for closing brackets/braces
            if (preg_match('/^[}\])]/', $line) || $line === '}' || $line === ')' || $line === ']') {
                $indent_level = max(0, $indent_level - 1);
            }
        } elseif ($phase === 'after') {
            // Increase indent for opening brackets/braces
            if (preg_match('/[{\[(]\s*$/', $line) || substr($line, -1) === '{' || substr($line, -1) === '(' || substr($line, -1) === '[') {
                $indent_level++;
            }
        }
    }

    /**
     * Process inline markdown (bold, italic, code, links)
     */
    private function process_inline_markdown($text) {
        // Bold
        $text = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $text);
        // Italic
        $text = preg_replace('/\*([^*]+)\*/', '<em>$1</em>', $text);
        // Inline code
        $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
        // Links
        $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $text);

        return $text;
    }

    /**
     * Parse markdown content to HTML
     */
    public function parse_markdown($content) {
        if (empty($content)) {
            return '';
        }

        // Store code blocks temporarily to prevent interference
        $code_blocks = array();
        $code_block_counter = 0;

        // FIRST: Process code blocks with ONLY copy (no language tag) - PRESERVE FORMATTING
        $content = preg_replace_callback('/```\s*copy\s*\n(.*?)```/s', function($matches) use (&$code_blocks, &$code_block_counter) {
            // ABSOLUTELY NO BEAUTIFICATION - preserve exact formatting for copy-only blocks
            $raw_code = $matches[1];
            $code = htmlspecialchars($raw_code, ENT_QUOTES, 'UTF-8');
            $copyButton = '<button class="code-copy-btn" onclick="copyCodeBlock(this)" title="Copy code">copy</button>';
            $code_block_html = '<div class="code-block-wrapper has-copy-button" data-code="' . esc_attr($raw_code) . '">' . $copyButton . '<pre style="line-height:1.1!important;font-size:15px!important;"><code style="line-height:1.1!important;background:none!important;font-size:15px!important;">' . $code . '</code></pre></div>';
            
            // Store the code block and return a placeholder
            $placeholder = '<!--CODEBLOCK_' . $code_block_counter . '-->';
            $code_blocks[$code_block_counter] = $code_block_html;
            $code_block_counter++;
            
            return $placeholder;
        }, $content);
        
        // SECOND: Process code blocks with language tags (and optionally copy button) 
        $content = preg_replace_callback('/```([a-zA-Z][a-zA-Z0-9+-]*)\s*(copy)?\s*\n(.*?)```/s', function($matches) use (&$code_blocks, &$code_block_counter) {
            $language = strtolower($matches[1]); // Language (javascript, php, etc.)  
            $hasCopy = !empty($matches[2]); // Check if 'copy' is specified
            
            // Beautify code content (only blocks with actual language tags reach here)
            $raw_code = $matches[3];
            $beautified_code = $this->beautify_code($raw_code, $language);
            $code = htmlspecialchars($beautified_code, ENT_QUOTES, 'UTF-8');
            
            $copyButton = '';
            $wrapperClass = 'code-block-wrapper';
            $codeClass = '';
            
            // Add language class if specified
            if (!empty($language)) {
                $codeClass = ' class="language-' . esc_attr($language) . '"';
                $wrapperClass .= ' language-' . esc_attr($language);
            }
            
            if ($hasCopy) {
                $wrapperClass .= ' has-copy-button';
                $copyButton = '<button class="code-copy-btn" onclick="copyCodeBlock(this)" title="Copy code">copy</button>';
            }
            
            $code_block_html = '<div class="' . $wrapperClass . '" data-code="' . esc_attr($beautified_code) . '">' . $copyButton . '<pre style="line-height:1.1!important;font-size:15px!important;"><code' . $codeClass . ' style="line-height:1.1!important;background:none!important;font-size:15px!important;">' . $code . '</code></pre></div>';
            
            // Store the code block and return a placeholder
            $placeholder = '<!--CODEBLOCK_' . $code_block_counter . '-->';
            $code_blocks[$code_block_counter] = $code_block_html;
            $code_block_counter++;
            
            return $placeholder;
        }, $content);
        
        // Process simple code blocks without language specification (fallback for any remaining)
        $content = preg_replace_callback('/```\s*\n(.*?)```/s', function($matches) use (&$code_blocks, &$code_block_counter) {
            // No beautification for code blocks without language tags
            $raw_code = $matches[1];
            $code = htmlspecialchars($raw_code, ENT_QUOTES, 'UTF-8');
            $code_block_html = '<div class="code-block-wrapper" data-code="' . esc_attr($raw_code) . '"><pre style="line-height:1.1!important;font-size:15px!important;"><code style="line-height:1.1!important;background:none!important;font-size:15px!important;">' . $code . '</code></pre></div>';
            
            // Store the code block and return a placeholder
            $placeholder = '<!--CODEBLOCK_' . $code_block_counter . '-->';
            $code_blocks[$code_block_counter] = $code_block_html;
            $code_block_counter++;
            
            return $placeholder;
        }, $content);

        // Process markdown syntax line by line
        $lines = explode("\n", $content);
        $result = array();

        foreach ($lines as $line) {
            $trimmed_line = trim($line);

            // Skip empty lines
            if (empty($trimmed_line)) {
                $result[] = '';
                continue;
            }

            // Headers
            if (preg_match('/^(#{1,6})\s+(.+)$/', $trimmed_line, $matches)) {
                $level = strlen($matches[1]);
                $text = $this->process_inline_markdown($matches[2]);
                $result[] = "<h{$level}>{$text}</h{$level}>";
                continue;
            }

            // Lists
            if (preg_match('/^-\s+(.+)$/', $trimmed_line, $matches)) {
                $text = $this->process_inline_markdown($matches[1]);
                $result[] = "<li>{$text}</li>";
                continue;
            }

            if (preg_match('/^\d+\.\s+(.+)$/', $trimmed_line, $matches)) {
                $text = $this->process_inline_markdown($matches[1]);
                $result[] = "<li>{$text}</li>";
                continue;
            }

            // Blockquotes
            if (preg_match('/^>\s+(.+)$/', $trimmed_line, $matches)) {
                $text = $this->process_inline_markdown($matches[1]);
                $result[] = "<blockquote><p>{$text}</p></blockquote>";
                continue;
            }

            // Regular paragraph
            $processed_line = $this->process_inline_markdown($trimmed_line);
            $result[] = "<p>{$processed_line}</p>";
        }

        $content = implode("\n", $result);

        // Wrap consecutive list items in ul tags
        $content = preg_replace('/(<li>.*?<\/li>(?:\s*<li>.*?<\/li>)*)/s', '<ul>$1</ul>', $content);

        // Clean up empty paragraphs
        $content = preg_replace('/<p><\/p>/', '', $content);

        // Restore code blocks from placeholders (do this LAST to prevent interference)
        foreach ($code_blocks as $index => $code_block_html) {
            $content = str_replace('<!--CODEBLOCK_' . $index . '-->', $code_block_html, $content);
        }

        return $content;
    }

    /**
     * Render the markdown block
     */
    public function render_markdown_block($attributes) {
        $content = isset($attributes['content']) ? $attributes['content'] : '';

        if (empty($content)) {
            return '<div class="markdown-block"><p><em>Enter your markdown content...</em></p></div>';
        }

        $html = $this->parse_markdown($content);

        return '<div class="markdown-block">' . $html . '</div>';
    }
}

// Initialize the plugin
new SimpleMarkdown();
