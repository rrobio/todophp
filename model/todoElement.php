<?php
declare(strict_types=1);
namespace model;

use DOMDocument;
use DOMElement;
use DOMException;

/**
 * @throws DOMException
 */
function _generateCheck(DOMDocument $dom, Todo $todo, string $type): DOMElement {
    $span = $dom->createElement('span');
    $toggleButton = $dom->createElement('input');
    $label = $dom->createElement('label');
    $labelCheck = $dom->createElement('input');
    $buttonID = "$type-button#$todo->id";

    $toggleButton->setAttribute('type', 'submit');
    $toggleButton->setAttribute('id', $buttonID);
    $toggleButton->setAttribute('value',"$todo->id");
    $toggleButton->setAttribute('name', "toggle$type");
    $toggleButton->setAttribute('style', 'display: none');

    $toggleButton->setAttribute('onChange', 'this.form.submit()');

    $labelCheck->setAttribute('type', 'checkbox');
    if ($type === 'done' && $todo->done) {
       $labelCheck->setAttribute('checked', '1');
    }
    if ($type === 'skip' && $todo->skip) {
        $labelCheck->setAttribute('checked', '1');
    }

    $label->setAttribute('for', $buttonID);
    $label->append($labelCheck);
    $label->append($dom->createTextNode($type));

    $span->append($toggleButton, $label);
    return $span;
}

/**
 * @throws DOMException
 */
function _generateDeleteButton(DOMDocument $dom, Todo $todo): DOMElement {
    $span = $dom->createElement('span');
    $input = $dom->createElement('button');
    $label = $dom->createElement('label');
    $buttonID = "delete#$todo->id";

    $input->setAttribute('type', 'submit');
    $input->setAttribute('value', "$todo->id");
    $input->setAttribute('name', 'deleteID');

    $input->setAttribute('id', $buttonID);
    $label->setAttribute('for', $buttonID);
    $label->append($dom->createTextNode('Delete'));
    $input->textContent = 'X';

    $span->append($input, $label);
    return $span;
}

/**
 * @throws DOMException
 */
function _generateText(DOMDocument $dom, Todo $todo): DOMElement {
    $text = $dom->createElement('p');
    $text->appendChild($dom->createTextNode($todo->text));
    return $text;
}
function generateNode(Todo $todo): DOMDocument {
    $dom = new DOMDocument('1.0', 'UTF-8');
    try {
        $div = $dom->createElement('div');
        $div->setAttribute('class', 'todo');

        $form = $dom->createElement('form');
        $form->setAttribute('action', 'index.php');

        $form->append(_generateCheck($dom, $todo, 'Done'),
                     _generateCheck($dom, $todo, 'Skip'),
                     _generateDeleteButton($dom, $todo),
                     _generateText($dom, $todo)
        );
        $div->append($form);
        $dom->append($div);
        return $dom;
    } catch (DOMException $e) {
        $dom->textContent = (string)$e;
        return $dom;
    }
}
