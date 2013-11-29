<?php
namespace Widget\Grid\Filter;
use Widget\Grid\Storage\AbstractStorage;

/**
 * Клас фильтра колонки (диапазон дат)
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class DateRangeFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $column = $this->getColumn()->getName();
        $grid = $this->getGrid();
        $value = $this->getValue();
        $from = isset($value['from']) ? $value['from'] : '';
        $to = isset($value['to']) ? $value['to'] : '';

        $attribs = array(
            'class' => 'input-text',
            'onkeypress' => $grid->getJavascriptObject() . '.doFilterEnter(event);'
        );

        $html = '<div class="range">';
        $html .= '<div class="range-line date form-control"><input placeholder="c" name="'.$column . '[from]" /></div>';
        $html .= '<div class="range-line date form-control"><input placeholder="по" name="'.$column . '[to]" /></div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(AbstractStorage $store)
    {
        $value = $this->getValue();
        if (!empty($value)) {
            $from = isset($value['from']) ? $value['from'] : '';
            $to = isset($value['to']) ? $value['to'] : '';

            $from && $from .= ' 0:00:00';
            $to && $to .= ' 23:59:59';

            //если таймстемп

            if (A1_Core::db() instanceof Zend_Db_Adapter_Oracle) {
                $from && $from = new Zend_Db_Expr(A1_Core_Date::dbDateTime($from));
                $to && $to = new Zend_Db_Expr(A1_Core_Date::dbDateTime($to));
            } else {
                $from && $from = new Zend_Db_Expr('"' . A1_Core_Date::dbDateTime($from) . '"');
                $to && $to = new Zend_Db_Expr('"' . A1_Core_Date::dbDateTime($to) . '"');
            }

            $from != '' && $store->addFilter($this->getColumn()->getName() . '_from', $this->getField(), $from, ' >= ?');
            $to != '' && $store->addFilter($this->getColumn()->getName() . '_to', $this->getField(), $to, ' <= ?');
        }

        return $this;
    }
}