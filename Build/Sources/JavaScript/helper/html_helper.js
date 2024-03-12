function createHtmlElementFromString(text) {
    const tempWrapper = document.createElement('div');
    tempWrapper.innerHTML = text;
    return tempWrapper;
}

function findParentBySelector(element, parentSelector) {
    if (element.parentElement.tagName.toLowerCase() === parentSelector.toLowerCase()) {
        return element.parentElement;
    } else {
        return findParentBySelector(element.parentElement, parentSelector);
    }
}

function replaceHtmlElementByIdentifier(responseAsHtml, identifier) {
    let existingElement = document.querySelector(identifier);
    if (!existingElement) return;
    let newElement = responseAsHtml.querySelector(identifier);
    existingElement.parentNode.replaceChild(newElement, existingElement);
}

export {createHtmlElementFromString, findParentBySelector, replaceHtmlElementByIdentifier};