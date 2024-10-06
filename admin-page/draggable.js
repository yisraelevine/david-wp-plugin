class Draggable {
    /** 
     * Holds the selected category element.
     * 
     * This property is defined when the user starts dragging an element 
     * and is reset to `null` when the dragging ends.
     * 
     * Is defined using the function `getSelectedCategory` 
     * 
     * @type {HTMLElement | null}
     */
    startingCategory = null;

    constructor(itemsContainer, categoriesContainer, selectCategory, callback, getSelectedCategory) {
        this.itemsContainer = itemsContainer
        this.categoriesContainer = categoriesContainer
        this.selectCategory = selectCategory
        this.callback = callback
        this.getSelectedCategory = getSelectedCategory

        this.itemsContainer.addEventListener('dragstart', this.start)
        window.addEventListener('mousemove', this.mouseMoveEvent)
        window.addEventListener('touchmove', this.touchMoveEvent, { passive: false })
        window.addEventListener('mouseup', this.end)
        window.addEventListener('touchend', this.end)
    }

    mouseMoveEvent = (event) => {
        if (!this.activeElement) return
        this.drag(event.target, event.clientX, event.clientY)
    }

    touchMoveEvent = (event) => {
        if (!this.activeElement) return
        event.preventDefault()
        const touches = event.targetTouches[0]
        const target = document.elementFromPoint(touches.clientX, touches.clientY)
        this.drag(target, touches.clientX, touches.clientY)
    }

    start = (event) => {
        const { clientX, clientY, target } = event
        if (!this.isDraggable(target)) return
        event.preventDefault()
        const { x, y } = target.getBoundingClientRect()
        this.setOffset(clientX - x, clientY - y)
        this.setActive(target)
    }

    drag = (target, clientX, clientY) => {
        if (this.isCategory(target)) {
            this.changeCategory(target)
        }
        else if (this.isDraggable(target)) {
            this.setBase(target)
            this.move(target)
            if (this.movingItem) {
                this.movingItem.remove()
                this.movingItem = null
                this.activeElement.style = ''
            }
        }
        this.activeElement.style.transform = `translate(${clientX - this.baseX}px, ${clientY - this.baseY}px)`
    }

    move(target) {
        const referenceNode = {
            [Node.DOCUMENT_POSITION_FOLLOWING]: target,
            [Node.DOCUMENT_POSITION_PRECEDING]: target.nextElementSibling,
        }[target.compareDocumentPosition(this.activeElement)]
        this.keepScrollPosition(() => this.itemsContainer.insertBefore(this.activeElement, referenceNode))
        this.setTarget(target)
    }

    changeCategory(target) {
        this.selectCategory(target)
        const positionKey = {
            [Node.DOCUMENT_POSITION_FOLLOWING]: 'lastChild',
            [Node.DOCUMENT_POSITION_PRECEDING]: 'firstChild',
        }[this.startingCategory.compareDocumentPosition(target)]
        if (positionKey) {
            this.setBase(this.itemsContainer)
            this.activeElement.style = 'position: absolute; top: 0; left: 0;'
            this.movingItem = this.itemsContainer[positionKey]
            this.keepScrollPosition(() => this.itemsContainer.appendChild(this.activeElement))
        } else {
            this.setActive(this.itemsContainer.children[this.activeElementIndex])
        }

    }

    end = () => {
        if (!this.activeElement) return
        this.activeElement.classList.remove('dragging')
        this.activeElement.style.transform = ''
        if (this.activeElement !== this.itemsContainer.children[this.activeElementIndex] ||
            this.startingCategory !== this.getSelectedCategory()) this.callback(this.activeElement, this.targetElement)
        this.activeElement = null
        this.targetElement = null
        this.startingCategory = null
        this.movingItem = null
    }

    keepScrollPosition = (callback) => {
        const scrollY = window.scrollY
        callback()
        if (scrollY !== window.scrollY) window.scrollBy(0, scrollY - window.scrollY)
    }

    isDraggable = (element) => element.parentElement === this.itemsContainer

    isCategory = (element) => element.parentElement === this.categoriesContainer

    setBase = (element) => {
        const { x, y } = element.getBoundingClientRect()
        this.baseX = x + this.offsetX
        this.baseY = y + this.offsetY
    }

    setOffset = (x, y) => {
        this.offsetX = x
        this.offsetY = y
    }

    setActive = (element) => {
        this.activeElement = element
        element.classList.add('dragging')
        this.activeElementIndex = Array.from(this.itemsContainer.children).indexOf(element)
        this.startingCategory = this.getSelectedCategory()
        this.setBase(element)
    }

    setTarget = (element) => {
        this.targetElement = element
    }
}
