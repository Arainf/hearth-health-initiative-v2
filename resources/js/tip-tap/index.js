import { Editor, Node } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'

// Extensions
import Underline from '@tiptap/extension-underline'
import Strike from '@tiptap/extension-strike'
import CodeBlock from '@tiptap/extension-code-block'
import Link from '@tiptap/extension-link'
import Highlight from '@tiptap/extension-highlight'
import TextAlign from '@tiptap/extension-text-align'
import Superscript from '@tiptap/extension-superscript'
import Subscript from '@tiptap/extension-subscript'
import Image from '@tiptap/extension-image'
import {FontSize, TextStyle} from "@tiptap/extension-text-style";

/* ==============================
   Two Column Node
================================ */
export const TwoColumn = Node.create({
    name: 'twoColumn',
    group: 'block',
    content: 'block+',
    draggable: true,

    parseHTML() {
        return [{ tag: 'two-column' }]
    },

    renderHTML() {
        return [
            'two-column',
            { class: 'grid grid-cols-2 gap-6 my-4' },
            0,
        ]
    },
})

/* ==============================
   Page Config
================================ */
const PAGE_HEIGHT = 1122 // A4 @ ~96dpi

/* ==============================
   Factory Function
================================ */
export function createTiptapEditor({
                                       element,
                                       rulerElement = null,
                                       content = '',
                                       editable = false,
                                   }) {
    if (!element) {
        throw new Error('TipTap editor element is required')
    }

    const updateRuler = () => {
        if (!rulerElement) return

        const editorEl = document.querySelector('.ProseMirror')
        if (!editorEl) return

        const contentHeight = editorEl.scrollHeight
        const pageCount = Math.max(1, Math.ceil(contentHeight / PAGE_HEIGHT))

        console.log(pageCount);
        rulerElement.innerHTML = ''
        rulerElement.style.height = `${pageCount * PAGE_HEIGHT}px`

        for (let i = 1; i <= pageCount; i++) {
            const marker = document.createElement('div')
            marker.className = 'ruler-marker'
            marker.style.top = `${i * PAGE_HEIGHT}px`
            rulerElement.appendChild(marker)
        }
    }


    const editor = new Editor({
        element,
        content,
        editable,
        extensions: [
            StarterKit,
            Underline,
            Strike,
            CodeBlock,
            Highlight,
            Superscript,
            Subscript,
            Image,
            TextStyle,
            FontSize,
            TwoColumn,
            Link.configure({ openOnClick: false }),
            TextAlign.configure({
                types: ['heading', 'paragraph'],
            }),
        ],
        onCreate() {
            requestAnimationFrame(updateRuler)
        },
        onUpdate() {
            requestAnimationFrame(updateRuler)
        },
    })

    window.addEventListener('resize', updateRuler)

    /* ==============================
       Public API
    ================================ */
    return {
        editor,

        setEditable(value) {
            editor.setEditable(!!value)
        },

        setContent(html) {
            editor.commands.setContent(html, true)
            requestAnimationFrame(updateRuler)
        },

        getHTML() {
            return editor.getHTML()
        },

        destroy() {
            editor.destroy()
            window.removeEventListener('resize', updateRuler)
        },

        /* Toolbar actions */
        undo() { editor.chain().focus().undo().run() },
        redo() { editor.chain().focus().redo().run() },

        toggleBold() { editor.chain().focus().toggleBold().run() },
        toggleItalic() { editor.chain().focus().toggleItalic().run() },
        toggleUnderline() { editor.chain().focus().toggleUnderline().run() },
        toggleStrike() { editor.chain().focus().toggleStrike().run() },

        toggleSize(size){ editor.chain().focus().setFontSize(`${size}`).run() },

        toggleHighlight() { editor.chain().focus().toggleHighlight().run() },
        toggleHeading(level) {
            editor.chain().focus().toggleHeading({ level }).run()
        },

        toggleBulletList() {
            editor.chain().focus().toggleBulletList().run()
        },

        toggleOrderedList() {
            editor.chain().focus().toggleOrderedList().run()
        },

        setAlign(align) {
            editor.chain().focus().setTextAlign(align).run()
        },

        insertTwoColumn() {
            editor.chain().focus().insertContent(`
                <two-column>
                    <p>Left column</p>
                    <p>Right column</p>
                </two-column>
            `).run()
        },

        isActive(type, opts = {}) {
            return editor.isActive(type, opts)
        },
    }
}
