(function() {
    var el = wp.element.createElement;
    var RichText = wp.blockEditor.RichText;
    var PlainText = wp.blockEditor.PlainText;
    var registerBlockType = wp.blocks.registerBlockType;
    var TextareaControl = wp.components.TextareaControl;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var PanelBody = wp.components.PanelBody;
    
    // Don't register if Pro version block is already registered
    if (wp.blocks.getBlockType('simple-markdown/markdown-block')) {
        return;
    }

    registerBlockType('simple-markdown/markdown-block', {
        title: 'Markdown',
        icon: 'editor-code',
        category: 'formatting',
        description: 'Add markdown content that will be rendered as HTML.',

        

        // Support migration from Pro version back to Basic
        deprecated: [
            {
                attributes: {
                    content: { type: 'string', default: '' },
                    enableSyntaxHighlighting: { type: 'boolean', default: true },
                    theme: { type: 'string', default: 'default' },
                    enableExport: { type: 'boolean', default: true },
                    enableTables: { type: 'boolean', default: true },
                    enableBlockquotes: { type: 'boolean', default: true },
                    enableStrikethrough: { type: 'boolean', default: true },
                    fontSize: { type: 'string', default: 'normal' },
                    lineHeight: { type: 'string', default: 'normal' }
                },
                migrate: function(attributes) {
                    return {
                        content: attributes.content || ''
                    };
                },
                save: function() { return null; }
            }
        ],

        attributes: {
            content: {
                type: 'string',
                default: ''
            }
        },

        edit: function(props) {
            var content = props.attributes.content;

            function onChangeContent(newContent) {
                props.setAttributes({ content: newContent });
            }

            return el('div', { 
                className: 'markdown-block-editor',
                style: { width: '100%' }
            },
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Markdown Settings' },
                        el('p', {}, 'Enter your markdown content in the text area. It will be rendered as HTML on the frontend.')
                    )
                ),
                el('div', {
                    style: {
                        border: '1px solid #ddd',
                        borderRadius: '4px',
                        padding: '10px',
                        backgroundColor: '#f9f9f9',
                        width: '100%',
                        boxSizing: 'border-box'
                    }
                },
                    el('label', {
                        style: {
                            display: 'block',
                            marginBottom: '8px',
                            fontWeight: 'bold',
                            fontSize: '14px'
                        }
                    }, 'Markdown Content:'),
                    el('textarea', {
                        value: content,
                        onChange: function(event) {
                            onChangeContent(event.target.value);
                        },
                        placeholder: '# Your Markdown Here\n\n**Bold text** and *italic text*\n\n- List item 1\n- List item 2\n\n[Link](https://example.com)',
                        style: {
                            width: '100%',
                            minHeight: '200px',
                            fontFamily: 'monospace',
                            fontSize: '14px',
                            border: '1px solid #ccc',
                            borderRadius: '3px',
                            padding: '8px',
                            resize: 'vertical',
                            boxSizing: 'border-box'
                        }
                    })
                )
            );
        },

        save: function(props) {
            return null; // Dynamic block, rendered in PHP
        }
    });
})();
