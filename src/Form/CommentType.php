<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Comment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('author') //Enlevé pour voir si le commentaire sera enregistré et affiché correctememt
            ->add('content', TextareaType::class, [
                'label' => false
            ])
            //->add('createdAt')
            /*->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'id',
                'required' => true
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
